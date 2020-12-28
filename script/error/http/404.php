<?php
namespace sPHP;

$Application->StatusCode(404);

print HTML\UI\MessageBox(
	"
		<p>Sorry, the page you requested is not available.</p>
		<p>Please <a href=\"{$Application->URL("Contact/Form")}\">email us</a> if you think this is an error and abnormal.</p>
		<p><a href=\"{$Application->URL()}\">Click here</a> to continue.</p>
	",
	"HTTP 404: Document not found!"
);
?>