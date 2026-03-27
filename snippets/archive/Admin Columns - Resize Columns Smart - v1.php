/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: A TRIER
 * Source path: A TRIER/WP_ADMIN resize columns/WP_ADMIN Resize columns.php
 * Display name: WP_ADMIN Resize columns
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (dc66d982e479…, 3 membres)
 * Canonical exact group ID: 118
 * Version family: DUP ADMIN - Resize columns [smart] (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_ADMIN resize columns/WP_ADMIN Resize columns.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: head-injection
 * Dependances probables: jQuery
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 82 / 2670
 * Hash code normalise (sha256): dc66d982e47984083be0026000f243c4c2c436ee9d67df2db0881d6145e357a2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-resize-columns-smart__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-resize-columns-smart__v001.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: jQuery
 * Hooks WP: admin_head
 * Fonctions clefs: aucun
 * Selecteurs / IDs: .wp-list-table thead th, .resizing
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 105 / 3523
 * Empreinte code (sha256): af2720b185059011e9bd0f4eff10d2973526ff4985bd2939605977094bce0bca
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-resize-columns-smart__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-resize-columns-smart__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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
