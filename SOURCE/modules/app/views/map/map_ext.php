<style>
    #create-map-page .list-point-clouds, 
    #edit-map-page .list-point-clouds {
        z-index: 100;
        transition: .3s ease all;
        max-width: 30vw;
    }

    #create-map-page .list-point-clouds:not(.show), 
    #edit-map-page .list-point-clouds:not(.show) {
        transform: translateX(-100%);
    }

    #create-map-page .list-point-clouds .card-toggle, 
    #edit-map-page .list-point-clouds .card-toggle {
        top: 0.5rem;
        right: 0;
        transform: translateX(100%);
        background: #353f53;
        border-top-right-radius: .1875rem;
        border-bottom-right-radius: .1875rem;
        cursor: pointer;
    }

    #create-map-page .list-point-clouds .card-toggle:hover,
    #edit-map-page .list-point-clouds .card-toggle:hover {
        background: #262d3c;
    }

    #create-map-page .list-point-clouds.show .card-toggle i,
    #edit-map-page .list-point-clouds.show .card-toggle i {
        transform: rotate(180deg);
    }

    .leaflet-popup-content-wrapper,
    .leaflet-popup-tip {
        background: #262d3c;
    }

    .tab-content::-webkit-scrollbar,
    .list-point-clouds .list::-webkit-scrollbar {
        width: 0;
    }

    .point-cloud-title {
        text-overflow: ellipsis;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        line-height: 1.5;
    }

    /**DETAIL PAGE*/
    #map-detail-page .list-point-clouds {
        z-index: 99;
        transition: .3s ease all;
        height: 40vh;
        width: 100%;
        order: 2;
    }

    #map-detail-page .map-detail {
        z-index: 98;
        transition: .3s ease all;
        height: 60vh;
        width: 100%;
        order: 1;
    }

    @media(min-width:768px) {
        #map-detail-page .list-point-clouds {
            height: 100%;
            width: 30vw;
            order: 1;
        }

        #map-detail-page .map-detail {
            height: 100%;
            width: 70vw;
            order: 2;
        }
    }

    #map-detail-page .list-point-clouds .card-toggle:hover {
        background: #262d3c;
    }

    #map-detail-page .list-point-clouds.show .card-toggle i {
        transform: rotate(180deg);
    }

    #map-detail-page .point-object-on-map {
        border: 3px solid #999;
        object-fit: cover;
        position: relative;
    }

    #map-detail-page .point-object-on-map:before {
        content: ' ';
        display: block;
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translate(-50%, 3px);
        width: 0;
        height: 0;
        border: 5px solid transparent;
        border-top-color: #999;
    }

    #map-detail-page .point-object-on-map img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .labelHSTS {
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>