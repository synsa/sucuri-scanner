<?php
/*
Plugin Name: Sucuri Scanner
Plugin URI: http://sitecheck.sucuri.net/
Description: This plugin allows you to execute a remote malware scanner on your WordPres site. It will check for malware, spam, blacklisting and other security issues (htaccess redirections, hidden code, etc). And yes, it is free. Similar to the scan provided online at http://sitecheck.sucuri.net
Author: http://sucuri.net
Version: 1.1.1
Author URI: http://sucuri.net
*/

define('SUCURISCAN','sucuriscan');
define('SUCURISCAN_VERSION','1.1.1');
define( 'SUCURI_URL',plugin_dir_url( __FILE__ ));
define( 'SUCURI_IMG',SUCURI_URL.'images/');


/* Starting Sucuri Scan side bar. */
function sucuriscan_menu() 
{
    add_menu_page('Sucuri Scanner', 'Sucuri Scanner', 'manage_options', 
                  'sucuriscan', 'sucuri_scan_page', SUCURI_IMG.'menu-icon.png');
    add_submenu_page('sucuriscan', 'Sucuri Scanner', 'Sucuri Scanner', 'manage_options',
                     'sucuriscan', 'sucuri_scan_page');

    add_submenu_page('sucuriscan', 'Malware removal', 'Malware removal', 'manage_options',
                     'sucuriscan_removal', 'sucuri_removal_page');
}



function sucuri_removal_page()
{
    $U_ERROR = NULL;
    if(!current_user_can('manage_options'))
    {
        wp_die(__('You do not have sufficient permissions to access this page.') );
    }


    /* Hardening page. */
    echo '<div class="wrap">';
    echo '<h2>Sucuri Malware Removal</h2>';

    echo '<h3>Get your site 100% clean and malware/blacklist free.</h3>'; 

    echo "<hr />";

    echo "<p>If our scanner is identifying any security problem on your site, we can get that
    cleaned for you. Just sign up with us here: <a href='http://sucuri.net/signup'>http://sucuri.net/signup</a> and our team will take care of it for you.</p>";
    echo "<hr />";
    echo "<h3>Get your site cleaned in under 4 hours (3 simple steps)</h3>";
    echo "<ol>";
    echo "<li>Sign up here: <a href='http://sucuri.net/signup'>http://sucuri.net/signup</a></li>";
    echo "<li>Click on malware removal request (inside the support page)</li>";
    echo "<li>Done! Go grab a coffee and wait for us to get it done</li>";
    echo "</ol>";
    ?>
    <br /><br />
    <b>If you have any question about these checks or this plugin, contact us at support@sucuri.net or visit <a href="http://sucuri.net">http://sucuri.net</a></b>
   <br />

    </div>
    <?php
}



/* Sucuri malware scan page. */
function sucuri_scan_page()
{
    $U_ERROR = NULL;
    if(!current_user_can('manage_options'))
    {
        wp_die(__('You do not have sufficient permissions to access this page.') );
    }

    if(!function_exists('curl_init'))
    {
        wp_die(__('This plugin requires the CURL functions to be available. Please contact your hosting company to enable it.') );
    }


    if(isset($_POST['wpsucuri-doscan']))
    {
        sucuriscan_print_scan();
        return(1);
    }


    /* Setting's header. */
    echo '<div class="wrap">';
    echo '<h2>Sucuri Malware Scanner</h2>';

    echo '<h3>Execute an external malware scanner on your site, using the <a href="http://sucuri.net">Sucuri</a> scanner. It will alert you if your site is compromised with malware, blackhat spam, defaced, or with any security problem.</h3>'; 
    ?>

    <form action="" method="post">
    <input type="hidden" name="wpsucuri-doscan" value="wpsucuri-doscan" />
    <input class="button-primary" type="submit" name="wpsucuri_doscanrun" value="Scan this site now!" />
    </form>

    <br /><br />
    <b>If you have any question about these checks or this plugin, contact us at support@sucuri.net or visit <a href="http://sucuri.net">http://sucuri.net</a></b>
   <br />
    </div>

    <?php
}



function sucuriscan_print_scan()
{
    $docurl = curl_init();
    curl_setopt($docurl, CURLOPT_URL, "http://sitecheck.sucuri.net/scanner/?serialized&scan=".home_url());
    curl_setopt($docurl, CURLOPT_VERBOSE, 0);
    curl_setopt($docurl, CURLOPT_HEADER, 0);
    curl_setopt($docurl, CURLOPT_RETURNTRANSFER, 1);

    $doresult = curl_exec($docurl);
    $res = unserialize($doresult);

    echo '<div class="wrap">';
    echo '<h2>Sucuri Malware Scanner</h2>';
    echo "<h3>System info</h3>";
   
    echo "Site: ".$res['SCAN']['SITE'][0]."<br />\n";
    foreach($res['SCAN'] as $myscan)
    {
        echo $myscan[0] . " ". $myscan[1];
    }
    foreach($res['SYSTEM']['NOTICE'] as $notres)
    {
        if(is_array($notres))
        {
            echo $notres[0]. " ".$notres[1];
        }
        else
        {
            echo $notres."<br />\n";
        }
    }

    echo "<h3>Security Scan</h3>";
    if(!isset($res['MALWARE']))
    {
        echo "<p>Malware not identified.</p>";
        echo "<p>Malware: No.</p>";
        echo "<p>Malicious javascript: NO.</p>";
        echo "<p>Malicious iframes: NO.</p>";
        echo "<p>Suspicious redirections (htaccess): NO.</p>";
        echo "<p>Blackhat SEO Spam: NO.</p>";
        echo "<p>Anomaly detection: CLean.</p>";
    }
    else
    {
        echo "<p>Malware FOUND.</p>";
        print_r($res['MALWARE']);
    }

    echo "<h3>Blacklisting</h3>";
    foreach($res['BLACKLIST']['INFO'] as $blres)
    {
        echo "CLEAN: ".$blres[0]." ".$blres[1]."<br />";
    }
    foreach($res['BLACKLIST']['WARN'] as $blres)
    {
        echo "WARN: ".$blres[0]." ".$blres[1]."<br />";
    }

    echo "<h3>ALL</h3>";
    foreach($res as $mytop => $mytopval)
    {
        foreach($res[$mytop] as $typename => $type)
	{
		foreach($res[$mytop][$typename] as $entry)
		{
			if(!is_array($entry))
			{
				echo "$mytop: $typename: $entry\n<br />";
			}
			else
			{
				echo "$mytop: $typename: ".$entry[0]." (".$entry[1].")\n<br />";
			}
		}
	}
    }
    ?>
    <br /><br />
    <b>If you have any question about these checks or this plugin, contact us at support@sucuri.net or visit <a href="http://sucuri.net">http://sucuri.net</a></b>
    <br />
    </div>
    <?php
}



/* Sucuri's admin menu. */
add_action('admin_menu', 'sucuriscan_menu');


?>
