/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/admin/029__id-54__admin-resize-columns-smart.php
 * Display name: ADMIN - Resize columns [smart]
 * Scope: admin
 * Online snippet: oui
 * Online active: non
 * Online ID: 54
 * Online modified: 2025-02-26 11:24:33
 * Online revision: 14
 * Exact duplicate group: oui (dc66d982e479…, 3 membres)
 * Canonical exact group ID: 118
 * Version family: DUP ADMIN - Resize columns [smart] (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/admin/029__id-54__admin-resize-columns-smart.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: head-injection
 * Dependances probables: jQuery
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 96 / 3204
 * Hash code normalise (sha256): dc66d982e47984083be0026000f243c4c2c436ee9d67df2db0881d6145e357a2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__admin__admin-resize-columns-smart__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__admin__admin-resize-columns-smart__v3__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: jQuery
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Selecteurs / IDs: .wp-list-table thead th, .resizing
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 109 / 3711
 * Empreinte code (sha256): ac699150b5f42a5f2b00841f9e7843973612d720912bedd9c77f0b5d93a6902b
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__admin__admin-resize-columns-smart__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__admin__admin-resize-columns-smart__v3__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_columns_list
 * Clusters secondaires: post_footer_ui, frontend_ui_widget
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_columns_list=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: resize-columns
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Ajoute des grips aux colonnes dans les tableaux admin pour ajuster sa largeur
add_action( 'admin_head', function () {
    echo '<style>
        th {
            position: relative;
        }
        .column-resizer {
            position: absolute;
            right: -2px;
            top: 0;
            width: 8px; /* Augmente la largeur pour inclure le grip */
            height: 100%;
            cursor: col-resize;
            z-index: 10;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .column-resizer::before {
            content: "";
            display: block;
            width: 4px;
            height: 60%;
            background: repeating-linear-gradient(
                to bottom,
                #ccc 0,
                #ccc 25%,
                transparent 25%,
                transparent 50%
            );
            background-size: 4px 6px; /* Espacement entre les "grips" */
            border-radius: 2px;
        }
        .column-resizer:hover::before {
            background: repeating-linear-gradient(
                to bottom,
                #888 0,
                #888 25%,
                transparent 25%,
                transparent 50%
            );
        }
    </style>
    <script>
    (function ($) {
      $(document).ready(function () {
        $(".wp-list-table thead th").each(function () {
          var $th = $(this);
          var columnIndex = $th.index(); // Index de la colonne
          var savedWidth = localStorage.getItem("columnWidth_" + columnIndex); // Récupérer la largeur sauvegardée

          // Appliquer la largeur sauvegardée si elle existe
          if (savedWidth) {
            $th.css("width", savedWidth + "px");
          }

          var $resizer = $("<div>", { class: "column-resizer" }).appendTo($th);

          $resizer.on("mousedown", function (e) {
            e.preventDefault();
            var startX = e.pageX;
            var startWidth = $th.outerWidth();

            $(document).on("mousemove.resizing", function (e) {
              var newWidth = startWidth + (e.pageX - startX);
              if (newWidth > 50) { // Largeur minimale
                $th.css("width", newWidth + "px");
              }
            });

            $(document).on("mouseup.resizing", function () {
              // Enregistrer la largeur choisie dans localStorage
              localStorage.setItem("columnWidth_" + columnIndex, $th.outerWidth());
              $(document).off(".resizing");
            });
          });
        });
      });
    })(jQuery);
    </script>';
} );
