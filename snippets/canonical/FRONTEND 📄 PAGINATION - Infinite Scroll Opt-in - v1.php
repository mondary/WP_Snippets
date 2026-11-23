<?php
/*
 * FRONTEND 📄 PAGINATION - Infinite Scroll Opt-in - v1
 * Pagination Kadence par défaut ; bouton opt-in pour charger les pages suivantes au scroll.
 */

if (!defined('ABSPATH')) exit;

/*
 * Display name: FRONTEND 📄 PAGINATION - Infinite Scroll Opt-in - v1
 * Title: Pagination — défilement infini opt-in (v1)
 * Scope: front-end
 * Resume fonctionnalites: Pagination serveur par défaut + bouton opt-in « Défilement infini » ; fetch des pages suivantes au scroll, images LiteSpeed réhydratées, reprise sur erreur.
 * Features detectees: pagination, infinite scroll, opt-in, LiteSpeed, accessibilité
 * Description: v1 — la pagination serveur reste le défaut (SEO, cache,
 *              chargement). Un bouton « Défilement infini » à côté de la
 *              pagination active le fetch des pages /page/N/ à l'approche
 *              du bas, avec préférence localStorage, déduplication,
 *              réhydratation des images LiteSpeed, fin de liste et reprise
 *              sur erreur. Désactiver après chargement recharge la page
 *              pour restaurer une pagination cohérente.
 * Hooks WP: navigation_markup_template, wp_head, wp_footer
 * Fonctions clefs: closures uniquement (aucune fonction globale)
 * Statut: actif
 */

add_filter(
	'navigation_markup_template',
	static function ( $template, $class ) {
		if ( 'pagination' !== $class ) {
			return $template;
		}

		if ( ! is_home() && ! is_archive() && ! is_search() ) {
			return $template;
		}

		$button = sprintf(
			'<button type="button" class="md-infinite-toggle" data-md-infinite-toggle aria-controls="archive-container" aria-pressed="false" hidden>%s</button>',
			esc_html( 'Défilement infini' )
		);

		return '<div class="md-pagination-tools">' . $template . $button . '</div>';
	},
	10,
	2
);

add_action(
	'wp_head',
	static function () {
		if ( ! is_home() && ! is_archive() && ! is_search() ) {
			return;
		}
		?>
<style id="md-infinite-css">
.md-pagination-tools{display:flex;align-items:center;justify-content:center;flex-wrap:wrap;gap:.75rem;margin-block:1.5rem}
.md-pagination-tools .navigation.pagination{margin:0}
.md-infinite-toggle{appearance:none;background:transparent;border:1px solid currentColor;border-radius:999px;color:inherit;cursor:pointer;font:inherit;line-height:1.2;padding:.5rem 1rem}
.md-infinite-toggle:hover{text-decoration:underline}
.md-infinite-toggle[aria-pressed="true"]{box-shadow:inset 0 0 0 2px currentColor;font-weight:600}
.md-infinite-toggle[hidden]{display:none}
.md-infinite-status{margin:0;flex-basis:100%;text-align:center;font-size:.875em;opacity:.8}
.md-infinite-status:empty{display:none}
.md-infinite-retry{appearance:none;background:transparent;border:0;color:inherit;cursor:pointer;font:inherit;text-decoration:underline}
.md-infinite-retry[hidden]{display:none}
.md-infinite-sentinel{height:1px;margin:0;padding:0}
.md-infinite-active .md-pagination-tools .navigation.pagination{display:none}
</style>
		<?php
	}
);

add_action(
	'wp_footer',
	static function () {
		if ( ! is_home() && ! is_archive() && ! is_search() ) {
			return;
		}
		?>
<script id="md-infinite-js" data-no-optimize="1" data-no-defer="1">
(() => {
	'use strict';

	const list = document.getElementById('archive-container');
	const toggle = document.querySelector('[data-md-infinite-toggle]');
	if (!list || !toggle) return;

	const wrapper = toggle.closest('.md-pagination-tools');
	const pagination = wrapper && wrapper.querySelector('.navigation.pagination');
	if (!pagination) return;

	const nextLink = pagination.querySelector('a.next.page-numbers');
	if (!nextLink) {
		toggle.remove();
		return;
	}

	if (!('IntersectionObserver' in window)) return;

	const STORAGE_KEY = 'md-infinite-scroll';
	const LOAD_AHEAD = 600;
	const root = document.documentElement;
	const seen = new Set();
	const sentinel = document.createElement('div');
	const status = document.createElement('p');
	const retry = document.createElement('button');

	sentinel.className = 'md-infinite-sentinel';
	sentinel.setAttribute('aria-hidden', 'true');

	status.className = 'md-infinite-status';
	status.setAttribute('role', 'status');
	status.setAttribute('aria-live', 'polite');

	retry.type = 'button';
	retry.className = 'md-infinite-retry';
	retry.textContent = 'Réessayer';
	retry.hidden = true;

	list.after(sentinel);
	wrapper.append(status, retry);

	let enabled = false;
	let loading = false;
	let failed = false;
	let nextUrl = nextLink.href;
	let loadedPages = 0;
	let controller = null;

	const keyFor = (item) => {
		const link = item.querySelector('.entry-title a[href]');
		return link ? link.href.split('#')[0] : '';
	};

	for (const item of list.children) {
		const key = keyFor(item);
		if (key) seen.add(key);
	}

	const store = {
		get() {
			try {
				return window.localStorage.getItem(STORAGE_KEY);
			} catch (error) {
				return null;
			}
		},
		set(value) {
			try {
				window.localStorage.setItem(STORAGE_KEY, value);
			} catch (error) {
				// Storage unavailable: the preference simply does not persist.
			}
		}
	};

	const setStatus = (message) => {
		status.textContent = message;
	};

	const hydrateMedia = (container) => {
		container.querySelectorAll('img[data-src]').forEach((img) => {
			if (img.dataset.srcset) img.srcset = img.dataset.srcset;
			if (img.dataset.sizes) img.sizes = img.dataset.sizes;
			img.src = img.dataset.src;
			img.removeAttribute('data-src');
			img.removeAttribute('data-srcset');
			img.removeAttribute('data-sizes');
			img.removeAttribute('data-lazyloaded');
		});
		container.querySelectorAll('[data-bg]').forEach((el) => {
			el.style.backgroundImage = 'url("' + el.dataset.bg.replace(/"/g, '\\"') + '")';
			el.removeAttribute('data-bg');
		});
	};

	const observer = new IntersectionObserver((entries) => {
		if (entries.some((entry) => entry.isIntersecting)) maybeLoad();
	}, { rootMargin: LOAD_AHEAD + 'px 0px' });

	const loadNext = async () => {
		if (!enabled || loading || !nextUrl) return;

		loading = true;
		retry.hidden = true;
		list.setAttribute('aria-busy', 'true');
		setStatus('Chargement de la suite…');
		controller = new AbortController();

		try {
			const response = await fetch(nextUrl, {
				credentials: 'same-origin',
				headers: { Accept: 'text/html' },
				signal: controller.signal
			});
			if (!response.ok) throw new Error('HTTP ' + response.status);

			const html = await response.text();
			if (!enabled) return;

			const doc = new DOMParser().parseFromString(html, 'text/html');
			const nextList = doc.getElementById('archive-container');
			if (!nextList) throw new Error('Liste introuvable');

			const fragment = document.createDocumentFragment();
			for (const item of nextList.children) {
				const key = keyFor(item);
				if (key && seen.has(key)) continue;
				if (key) seen.add(key);
				fragment.append(document.importNode(item, true));
			}

			if (!fragment.childElementCount) {
				nextUrl = null;
				setStatus('Tous les articles sont chargés.');
				observer.disconnect();
				return;
			}

			hydrateMedia(fragment);
			list.append(fragment);
			loadedPages += 1;

			const nextPagination = doc.querySelector('.navigation.pagination');
			const newNext = nextPagination && nextPagination.querySelector('a.next.page-numbers');
			nextUrl = newNext ? newNext.href : null;

			if (nextUrl) {
				setStatus('');
			} else {
				setStatus('Tous les articles sont chargés.');
				observer.disconnect();
			}
		} catch (error) {
			if (error.name === 'AbortError') return;
			failed = true;
			setStatus('Impossible de charger la suite.');
			retry.hidden = false;
		} finally {
			loading = false;
			list.removeAttribute('aria-busy');
			controller = null;
		}
	};

	function maybeLoad() {
		if (enabled && nextUrl && !loading && !failed) loadNext();
	}

	const setEnabled = (on, persist) => {
		if (on && !nextUrl) return;

		enabled = on;
		failed = false;
		toggle.setAttribute('aria-pressed', String(on));
		root.classList.toggle('md-infinite-active', on);
		retry.hidden = true;

		if (persist !== false) store.set(on ? '1' : '0');

		if (!on) {
			if (controller) controller.abort();
			setStatus('');
			// Appended pages cannot be unloaded without breaking pagination.
			if (loadedPages > 0) window.location.reload();
			return;
		}

		setStatus('');
		// The observer only fires on a threshold change; catch an already-visible
		// sentinel when the mode is enabled (short archive, restored scroll).
		if (sentinel.getBoundingClientRect().top <= window.innerHeight + LOAD_AHEAD) maybeLoad();
	};

	observer.observe(sentinel);
	toggle.hidden = false;
	toggle.addEventListener('click', () => setEnabled(!enabled));
	retry.addEventListener('click', () => {
		failed = false;
		maybeLoad();
	});

	if (store.get() === '1') setEnabled(true, false);
})();
</script>
		<?php
	}
);
