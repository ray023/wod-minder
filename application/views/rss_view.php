<?php echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<channel>
		<title><?php echo $box_name;?></title>
		<atom:link href='<?php echo base_url().'index.php/feed/index/'.$box_id; ?>' rel="self" type="application/rss+xml"/>
		<link><?php echo $box_url;?></link>
		<description>Daily WOD feed for <?php echo $box_name;?> (Created By WOD-Minder)</description>
		<lastBuildDate><?php echo $last_build_date;?></lastBuildDate>
		<language>en-US</language>
		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>1</sy:updateFrequency>
		<generator>http://app.wod-minder.com</generator>
                    <?php echo $item_list_html; ?>
	</channel>
</rss>
