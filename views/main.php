<div class="wrap pg-container">

    <h1><a href="<?php echo PROGRIDS_BASE_URL; ?>" target="_blank"><img alt="Welcome to the ProGrids Widget Plugin" src="<?php echo PROGRIDS_PLUGIN_URL . '/images/proGridsHeader.png'; ?>" ></a></h1>

    <div class="pg-tutorial">
        <ol>
            <li><span>1</span>Create a ProGrids account <a href="<?php echo PROGRIDS_BASE_URL . '/register'; ?>" target="_blank" title="Register on ProGrids.com" class="links">here</a> to start creating Widgets.</li>
            <li><span>2</span>Create a widget.</li>
            <li><span>3</span>Copy and paste the installation code in the space below and press "Save".</li>
        </ol>
    </div>

    <form action="<?php echo admin_url('options.php'); ?>" method="post">
        <?php
        settings_fields(ProGrids::OPTION_GROUP_NAME);
        $code = get_option('progrids_code');
        ?>
        <label for="pg-code">Installation Code:</label>
        <textarea id="pg-code" name="progrids_code" class="pg-code"><?php echo $code ?></textarea>
        <button class="pg-button" type="submit">Save</button>
    </form>