<?php
/**
 * Default Layout - a Layout similar with the classic Header and Footer files.
 */

// Generate the Language Changer menu.
$language = Language::code();

$languages = Config::get('languages');
//print_r($languages);

//
ob_start();


?>


<!DOCTYPE html>
<html lang="<?= $language; ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title .' - ' .Config::get('app.name', SITE_TITLE); ?></title>
<?php
echo isset($meta) ? $meta : ''; // Place to pass data / plugable hook zone

Assets::css([
    vendor_url('dist/css/bootstrap.min.css', 'twbs/bootstrap'),
    vendor_url('dist/css/bootstrap-theme.min.css', 'twbs/bootstrap'),
    'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css',
    template_url('css/style.css', 'Default'),
]);

echo isset($css) ? $css : ''; // Place to pass data / plugable hook zone
?>
</head>
<body style='padding-top: 28px;'>



<?= isset($afterBody) ? $afterBody : ''; // Place to pass data / plugable hook zone ?>

<div class="container" align="center" >
    <p>
        <a href="http://localhost:8080/login"> <img style="min-width:100px;max-width:300px;" src='<?= template_url('images/login.png', 'Default'); ?>' alt='<?= Config::get('app.name', SITE_TITLE); ?>'>
    </a>

          <a href="http://localhost:8080/register"><img style="min-width:100px;max-width:300px;" src='<?= template_url('images/signup.png', 'Default'); ?>' alt='<?= Config::get('app.name', SITE_TITLE); ?>'>
        </a>
    </p>

    <?= $content; ?>
</div>

<footer class="footer" ">
    <div class="container-fluid" >
        <div class="row" style="margin: 15px 0 0;">
            <div class="col-lg-4">
                <p class="text-muted"> Par Florent Legru
            </div>
            <div class="col-lg-8">
                <p class="text-muted pull-right">
                    <?php if(Config::get('app.debug')) { ?>
                    <small><!-- DO NOT DELETE! - Profiler --></small>
                    <?php } ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php
Assets::js([
    'https://code.jquery.com/jquery-1.12.4.min.js',
    vendor_url('dist/js/bootstrap.min.js', 'twbs/bootstrap'),
]);

echo isset($js) ? $js : ''; // Place to pass data / plugable hook zone

echo isset($footer) ? $footer : ''; // Place to pass data / plugable hook zone
?>

<!-- DO NOT DELETE! - Forensics Profiler -->

</body>
</html>
