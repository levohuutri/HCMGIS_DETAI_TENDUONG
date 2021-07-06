<style>
    .objects-summary-item {
        cursor: pointer;

    }
    .objects-summary-item {
        cursor: pointer;
        height: 50px;
        border: 1px solid;
        padding-top: 11px;
        padding-left: 10px;
        /* vertical-align: middle; */
    }
    .objects-summary-item:hover {
        background: gray;
    }
</style>
<div>
    <?php foreach ($model as $key => $prop) : ?>
        <?php if ($prop['props']): ?>
            <div class="objects-summary-item" onclick="onObjectsSummaryItemClicked('<?= $prop['code'] ?>')"><b><?= $prop['props']['name'] ?></b> có <b><?= $prop['count'] ?></b> đối tượng</div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<script>
    var APP = APP || {};
    function onObjectsSummaryItemClicked(code) {
        APP.selectedObjectsSummaryCode = code;
        $(document).trigger('onObjectsSummaryClicked');
    }
</script>