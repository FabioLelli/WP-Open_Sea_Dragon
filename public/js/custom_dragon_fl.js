let overlays_fl = [];
let nodi = JSON.parse(fl_map.luoghi);


if (nodi.length > 0) {
    nodi.forEach(inserimentoOverlay);
}

function inserimentoOverlay(nodo) {
    overlay = {
        id : "overlay-" + nodo.link,
        x : parseFloat(nodo.x),
        y : parseFloat(nodo.y)
    }
    overlays_fl.push(overlay);
}

var viewer = OpenSeadragon({
        id: "dragon_map_fl",
        prefixUrl: fl_map.prefixUrl,
        homeFillsViewer : false,
        overlays : overlays_fl,
        tileSources: fl_map.tileSources
    });
 
if (nodi.length > 0) {
    nodi.forEach(inserimentoTracker);
}


function inserimentoTracker(nodo) {
    new OpenSeadragon.MouseTracker({
    element: 'overlay-' + nodo.link,
    clickHandler: function(e) {
        var $target = jQuery(e.originalTarget);
        if ($target.is('a')) {
            location.href = $target.attr('href');
        }
    }
    });
}