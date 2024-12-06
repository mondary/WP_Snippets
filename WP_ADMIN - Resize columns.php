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
              $(document).off(".resizing");
            });
          });
        });
      });
    })(jQuery);
    </script>';
} );
