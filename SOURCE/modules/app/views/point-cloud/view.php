<div class="position-relative h-100">
    <div class="potree_container" style="position: absolute; width: 100%; height: 100%; left: 0px; top: 0px; ">
		<div id="potree_render_area"></div>
		<div id="potree_sidebar_container"> </div>
	</div>
</div>

<script>
    var edlEnabled = '<?= $edlEnabled ?>' == '1' ? true : false;
    window.viewer = new Potree.Viewer(document.getElementById("potree_render_area"));

    viewer.setEDLEnabled(edlEnabled);
    viewer.setFOV(60);
    viewer.setPointBudget(1 * 1000 * 1000);
    // document.title = "";
    // // viewer.setEDLEnabled(false);
    // viewer.setBackground("gradient"); // ["skybox", "gradient", "black", "white"];
    // viewer.setDescription("");
    viewer.loadSettingsFromURL();

    viewer.loadGUI(() => {
        viewer.setLanguage('en');
        $("#menu_appearance").next().show();
        $("#menu_tools").next().show();
        $("#menu_scene").next().show();
        // viewer.toggleSidebar();
    });

    Potree.loadPointCloud('<?= Yii::$app->homeUrl . 'pointclouds/' . $slug . '/cloud.js' ?>', "viewer", e => {
        let pointcloud = e.pointcloud;
        let material = pointcloud.material;
        let colorType = Potree.PointColorType.hasOwnProperty('<?= $colorType ?>') ? Potree.PointColorType['<?= $colorType ?>'] : Potree.PointColorType.ELEVATION;
        viewer.scene.addPointCloud(pointcloud);
        material.pointColorType = colorType; // any Potree.PointColorType.XXXX 
        material.size = 1;
        material.pointSizeType = Potree.PointSizeType.ADAPTIVE;
        material.shape = Potree.PointShape.SQUARE;
        viewer.fitToScreen();
    });
</script>