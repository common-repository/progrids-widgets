<?php
/**
 * @var $widgetId int - id of widget
 */
?>

<div id="proGrids<?php echo $widgetId ?>"></div>
<script type="text/javascript">
    (function() {
        var params = {
            id: "<?php echo $widgetId ?>"
        };
        var qs="";
        params.cb = (new Date).getTime();
        for(var key in params){qs+=key+"="+params[key]+"&"}
        qs=qs.substring(0,qs.length-1);
        var s = document.createElement("script");
        s.type= 'text/javascript';
        s.src = "//<?php echo PROGRIDS_WIDGETS_HOST ?>/widget?" + qs;
        s.async = true;
        document.getElementById("proGrids<?php echo $widgetId ?>").appendChild(s);
    })();
</script>