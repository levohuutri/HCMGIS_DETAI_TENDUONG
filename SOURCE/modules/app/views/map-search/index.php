<?php

use app\modules\contrib\gxassets\GxJqueryAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxLimitlessTemplateAsset;

GxLimitlessTemplateAsset::register($this);
GxJqueryAsset::register($this);
GxLeafletAsset::register($this);
?>
<script>
    var APP = {homeUrl: "<?= Yii::$app->homeUrl ?>"};
</script>

<div class="col-md-12">
    <div class='row'>
        <div class='col-md-4'>
            <div class="row" id="objects" style="height: 50%"></div>
            <div class="row" id="keywords" style="height: 50%"></div>
        </div>
        <div class="col-md-8">
            <div id="map">
                
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        initElements();
        initEvents();
    });
    function loadAjaxToDiv(selector, url) {
        let $ele = $(selector);
        $.ajax({
            url: url,
            success: function(html) {
                $ele.empty().append(html);
            }
        })
    }

    function initElements() {
        loadAjaxToDiv("#map", APP.homeUrl + "app/map-search/map");
        loadAjaxToDiv("#objects", APP.homeUrl + "app/map-search/objects");
        loadAjaxToDiv("#keywords", APP.homeUrl + "app/map-search/keywords");
    }

    function initEvents() {
        $(document).on("onMapMoveEnd", function() {
            loadObjects();
            loadKeywords();
        })

        $(document).on("onMapZoomEnd", function() {
            loadObjects();
            loadKeywords();
        })
    }

    function loadObjects() {
        let param = getMapParams();
        let url = APP.homeUrl + "app/map-search/objects?" + param;
        loadAjaxToDiv("#objects", url);
    }

    function loadKeywords() {
        let param = getMapParams();
        let url = APP.homeUrl + "app/map-search/objects?" + param;
        loadAjaxToDiv("#keywords", url);
    }

    function getMapParams() {
        let obj = {
            northEast: APP.map.getBounds()._northEast,
            southWest: APP.map.getBounds()._southWest
        }
        return jQuery.param({
            northEastLat: obj.northEast.lat,
            northEastLng: obj.northEast.lng,
            southWestLat: obj.southWest.lat,
            southWestLng: obj.southWest.lng,
        })
    }
</script>