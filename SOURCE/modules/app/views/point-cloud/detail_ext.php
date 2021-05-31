<style>
    body {
        overflow-y: unset;
    }

    body.point-fullscreen {
        overflow-y: hidden
    }

    .pointcloud-wrap {
        position: relative;
        z-index: 100;
        height: 500px;
        /* transition: .5s ease all; */
    }

    .pointcloud-wrap.point-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .btn-fullscreen {
        position: absolute;
        top: .5rem;
        right: .5rem;
        z-index: 101;
    }

    .btn-fullscreen:hover i {
        transform: scale(1.2)
    }

    .point-cloud-tag {
        border: solid 1px #FF7043;
        border-radius: .1875rem;
        padding: .1875rem .325rem;
        color: #FF7043 !important;
    }

    .point-cloud-tag:hover {
        background-color: #FF7043;
        color: #fff !important;
    }

    a:hover, a:visited, a:link, a:active { color: #8dccff; }

    a:hover { color: #74c1ff; }
</style>