<?php
if($_POST['create_new'] == 'true')
{
	if(empty($_POST['pageid']))
	{
		echo '<div class="error"><p><strong>Please select pages and/or posts before adding Unlocker</strong></p></div>';
	}
	elseif(empty($_POST['unlocker_code']))
	{
		echo '<div class="error"><p><strong>Cannot add empty unlocker.</strong></p></div>';
	}
	else
	{
		// insert the option into the table
		$pages=implode(',',$_POST['pageid']);
		
		$sql="INSERT INTO ".DB_TABLE." (Pages, Code, Notes) VALUES ('".mysql_real_escape_string($pages)."', '".mysql_real_escape_string($_POST['unlocker_code'])."', '".mysql_real_escape_string($_POST['unlocker_notes'])."')";
		mysql_query($sql);
		echo '<div class="updated"><p><strong>Unlocker Added</strong></p></div>';
	}
}
elseif($_POST['submit_action'] == 'Update')
{
	if(!empty($_POST['unlocker_id']))
	{
		$pages=implode(',',$_POST['pageid']);
		mysql_query("UPDATE ".DB_TABLE." SET Pages='".mysql_real_escape_string($pages)."', Code='".mysql_real_escape_string($_POST['unlocker_code'])."', Notes='".mysql_real_escape_string($_POST['unlocker_notes'])."' WHERE Id=".$_POST['unlocker_id']);
		echo '<div class="updated"><p><strong>Unlocker Updated</strong></p></div>';
	}
}
elseif($_POST['submit_action'] == 'Delete')
{
	if(!empty($_POST['unlocker_id']))
	{
		mysql_query("DELETE FROM ".DB_TABLE." WHERE Id=".$_POST['unlocker_id']);
		echo '<div class="updated"><p><strong>Unlocker Deleted</strong></p></div>';
	}
}
elseif($_POST['submit_action'] == 'Activate')
{
	if(!empty($_POST['unlocker_id']))
	{
		mysql_query("UPDATE ".DB_TABLE." SET Status='1' WHERE Id=".$_POST['unlocker_id']);
		echo '<div class="updated"><p><strong>Unlocker Activated</strong></p></div>';
	}
}
elseif($_POST['submit_action'] == 'Deactivate')
{
	if(!empty($_POST['unlocker_id']))
	{
		mysql_query("UPDATE ".DB_TABLE." SET Status='0' WHERE Id=".$_POST['unlocker_id']);
		echo '<div class="updated"><p><strong>Unlocker Deactivated</strong></p></div>';
	}
}

function generateDropDownOptions($selected_vals=array())
{
	if(!is_array($selected_vals))
	{
		$selected_vals=array();
	}
	$pages=get_pages();
	$posts = get_posts(array(
		'numberposts' => -1
	));
	
	ob_start();
?>
<optgroup label="---- Pages ----">
	<option value="page_0"<?= in_array('page_0',$selected_vals) ? ' selected="selected"' : '' ?>>Home</option>
<?
foreach($pages as $page)
{
	$val='page_'.$page->ID;
	$attr=array();
	if(in_array($val, $selected_vals))
	{
		$attr[]=' selected="selected"';
	}
?>
	<option value="<?= $val ?>"<?= implode('',$attr) ?>><?= $page->post_title ?></option>
<?
}
?>
</optgroup>
<optgroup label="---- Posts ----">
<?
foreach($posts as $post)
{
	$val='post_'.$post->ID;
	$attr=array();
	if(in_array($val, $selected_vals))
	{
		$attr[]=' selected="selected"';
	}
?>
	<option value="<?= $val ?>"<?= implode('',$attr) ?>><?= $post->post_title ?></option>
<?
}
?>
</optgroup>
<?
	return ob_get_clean();
}

?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>Leadbolt Content Unlocker</h2>
	<p class="description">LeadBolt's content unlocking technologies are simple, easy to install and provide publishers with a revolutionary approach to making real money from their website traffic or website content! To use this plugin you must have already signed up with <a href="http://leadbolt.com" target="_blank">leadbolt.com</a> and created one of more content unlockers in your account and downloaded the html code using the Get Code function. </p><br />

<?
// now get all the added options...
$res=mysql_query('SELECT * FROM '.DB_TABLE);
if($res !== false && mysql_num_rows($res))
{
?>
<h2>Existing Unlockers</h2>
<p class="description">The following <a href="http://leadbolt.com" target="_blank">Leadbolt.com</a> content unlockers are already defined for your wordpress blog. Use the function listed to update, delete, activate or deactivate a specific unlocker.</p><br />
<?
	$count=1;
	while($row = mysql_fetch_assoc($res))
	{
		$selected_vals=explode(',',$row['Pages']);
?>
<form method="post" action="">
	<input type="hidden" name="update" value="true" />
	<input type="hidden" name="unlocker_id" value="<?= $row['Id'] ?>" />
	<table class="widefat" style="width:100%;">
		<tr style="background-color:<?= ($row['Status'] == '1') ?'#98CF8E' : '#BBB' ?>"><td>Leadbolt unlocker # <?= $count++ ?></td><td colspan="2" align="right">
			This unlocker is <strong><?= ($row['Status'] == '0') ? 'OFFLINE' : 'ACTIVE' ?></strong>&nbsp;&nbsp;<input type="submit" name="submit_action" value="<?= (($row['Status'] == '1') ? 'Dea' : 'A') ?>ctivate" class="button-primary"/>
		</td></tr>
		<tr>
			<th style="width:20%;">Selected post/page</th>
			<th style="width:50%;">Leadbolt html unlocker code</th>
			<th style="width:30%;">Notes (optional)</th>
		</tr>
		<tr>
			<td><select name="pageid[]" multiple="multiple" size="8" style="height:140px;width:220px;"><?= generateDropDownOptions($selected_vals) ?></select>
			<td><textarea name="unlocker_code" rows="6" cols="30" style="width:550px;"><?= stripslashes($row['Code']) ?></textarea></td>
			<td><textarea name="unlocker_notes" rows="6" cols="10" style="width:330px;"><?= stripslashes($row['Notes']) ?></textarea></td>
		</tr>
		<tr><td align="right" colspan="3">
			<input type="submit" class="button-primary" name="submit_action" value="Update" /> &nbsp;
			<input type="submit" class="button-primary" name="submit_action" value="Delete" onclick="return confirm('Are you sure you want to delete this unlocker ??');" />
		</td></tr>
	</table>
</form><br />
<?
	}
}
?>

	
<h2>Add a New Unlocker</h2>
<p class="description">Use the form below to add the unlocker codes to your wordpress blog. For each unlocker, select the pages you wish to add the unlocker to (hold Ctrl key to select multiple entries). Please make sure that each wordpress page has only 1 unlocker defined.</p><br />

<form name="unlocker_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<input type="hidden" name="create_new" value="true">
	
	<table class="widefat" style="width:100%;">
		<tr>
			<th style="width:20%;">Selected post/pages</th>
			<th style="width:50%;">Leadbolt unlocker html code</th>
			<th style="width:30%;">Notes (optional)</th>
		</tr>
		<tr>
			<td><select name="pageid[]" multiple="multiple" size="8" style="height:140px;width:220px;"><?= generateDropDownOptions() ?></select>
			<td><textarea name="unlocker_code" rows="6" cols="30" style="width:550px;"></textarea></td>
			<td><textarea name="unlocker_notes" rows="6" cols="10" style="width:330px;"></textarea></td>
		</tr>
		<tr><td align="right" colspan="3"><input type="submit" class="button-primary" name="Submit" value="Add Unlocker" /></td></tr>
	</table>
</form>


<h2>Support</h2>
<p class="description">For support with this plug-in, please contact <a href="http://leadbolt.com" target="_blank">Leadbolt</a> directly through their contact us or support channels. This plugin and related technology - copyright &copy; 2010 <a href="http://leadbolt.com" target="_blank">Leadbolt.com</a> All rights reserved.</p><br/><br/>
</div>