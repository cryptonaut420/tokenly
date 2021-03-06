<h2>My Referrals</h2>
<p>
	<strong>My Affiliate Link: <a href="<?= SITE_URL ?>?ref=<?= $refLink ?>"><?= SITE_URL ?>?ref=<?= $refLink ?></a></strong>
</p>

<?php
if(count($refs) == 0){
	echo '<p>No referrals yet!</p>';
}
else{
	foreach($refs as &$row){
		$row['userlink'] = '<a href="'.SITE_URL.'/profile/user/'.$row['slug'].'" target="_blank">'.$row['username'].'</a>';
	}
	
	echo '<p><strong>Total Referrals:</strong> '.count($refs).'</p>';
	$table = $this->generateTable($refs, array('class' => 'admin-table mobile-table',
											   'fields' => array('userlink' => 'Username',
																 'refTime' => 'Referral Date'),
												'options' => array(array('field' => 'refTime', 'params' => array('functionWrap' => 'formatDate')),
																	
												)));
	
	echo $table->display();
	
}

?>
