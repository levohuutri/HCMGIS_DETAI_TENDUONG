<?php

use app\modules\contrib\gxassets\GxJqueryAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;
use app\modules\contrib\gxassets\GxLimitlessTemplateAsset;

GxLimitlessTemplateAsset::register($this);
GxJqueryAsset::register($this);
GxLeafletAsset::register($this);
?>
<script>
    var APP = {
        homeUrl: "<?= Yii::$app->homeUrl ?>",
        events: {
            onMapMoveEnd: "onMapMoveEnd",
            onMapZoomEnd: "onMapZoomEnd"
        }
    };
</script>

<div class="col-md-12">
    <div class='row'>
        <div class='col-md-3'>
            <div class="row" style="height: 50%">
                <div class="col-md-12">
                    <div class="col-md-12" style="font-weight: bold"><h3>THỐNG KẾ ĐỐI TƯỢNG TRONG KHU VỰC</h3></div>
                    <div class="col-md-12" id="objects"></div>
                </div>
            </div>
            <div class="row" id="keywords" style="height: 50%"></div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12" style="font-weight: bold"><h3>THÔNG TIN CHI TIẾT</h3></div>
            <div class="col-md-12" id="objects-detail">
                Chưa chọn loại đối tượng
            </div>
        </div>
        <div class="col-md-6">
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
        
        $(document).on("onObjectsSummaryClicked", function() {
            console.log(APP.selectedObjectsSummaryCode);
        });
    }

    function loadObjects() {
        let param = getMapParams();
        let url = APP.homeUrl + "app/map-search/objects-summary?" + param;
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

    $(document).on("onMapInited", function() {
        loadObjects();
        loadKeywords();
    })
</script>