<?php
    header("Content-type: text/css; charset: UTF-8");
?>
/* File: common.css.php */
/****************************************/

/* general tags */

/* tags for responsive design */
* {
    box-sizing: border-box;
}

.row:after {
	content: "";
	clear: both;
	display: block;
}

[class*="col-"] {
	float: left;
	padding: 15px;
}

/* For mobile phones: */
[class*="col-m"] {
        width: 100%;
}

@media only screen and (min-width: 600px) {
    /* For tablets: */
    .col-m-1 {width: 8.33%;}
    .col-m-2 {width: 16.66%;}
    .col-m-3 {width: 25%;}
    .col-m-4 {width: 33.33%;}
    .col-m-5 {width: 41.66%;}
    .col-m-6 {width: 50%;}
    .col-m-7 {width: 58.33%;}
    .col-m-8 {width: 66.66%;}
    .col-m-9 {width: 75%;}
    .col-m-10 {width: 83.33%;}
    .col-m-11 {width: 91.66%;}
    .col-m-12 {width: 100%;}
}
@media only screen and (min-width: 768px) {
    /* For desktop: */
    .col-1 {width: 8.33%;}
    .col-2 {width: 16.66%;}
    .col-3 {width: 25%;}
    .col-4 {width: 33.33%;}
    .col-5 {width: 41.66%;}
    .col-6 {width: 50%;}
    .col-7 {width: 58.33%;}
    .col-8 {width: 66.66%;}
    .col-9 {width: 75%;}
    .col-10 {width: 83.33%;}
    .col-11 {width: 91.66%;}
    .col-12 {width: 100%;}
}

/* end tags for responsive design */

html {
    font-size: 1em;
	color: #FFFFFF;
	font-family: Calibri;
}

a {
	color: #17731D;
}

body {
	background-color: #202020;
	padding: 0;
	margin: 0;
}

body#indexlogin {
	background-color: #5E6C5D;
}

div#page_content{
	margin-top: 0;
	padding: 0;
}

div#banner{
	margin: 0;
	border: 0;
	color: #9AD19E;
	line-height: 90%;
	background-color: #35A33C;
	padding: 5px 70px;
	text-shadow: -1px -1px #888;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#navbar {
	margin: 0;
	border: 0;
	padding-left: 40px;
	background-color: #35A33C;
}

div#right_float_wrapper {
		float: right;
		display: inline-block;
}

div#admin_block, div#logout_block  {
	font-size: 1.20em;
	line-height: 1.2em;
	font-weight: bold;
	color: #FFFFFF;
	display: inline;
	margin-left: 1.5em;
}

div#logout_block a:visited, div#admin_block a:visited{
	color: #FFFFFF;
}

div#navbar p.navcurrent{
	display: inline;
	color: #FFFFFF;
	padding: 0 40px;
	background-color: #B8EC69;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#navbar p.navcurrent a:visited{
	color: #6F5C19;
}

div#navbar p.navlink{
	display: inline;
	color: #8CBE3E;
	padding: 0 40px;
	background-color: #2B4207;
	margin-right: 5px;
	font-size: 120%;
	font-weight: bold;
}

div#navbar p.navlink a{
	color: #8CBE3E;
}

div#banner h1{
	font-size: 300%;
	text-shadow: 2px 2px #000 inset;
	-moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
}

div#container{
	margin: 0 2em;
	line-height: 75%;
}

body#indexlogin div#container{
	text-align: left;
	margin: 0 auto;
}



body#indexlogin div#container input[type=submit]{
	margin-left: auto;
	margin-right: auto;
	width: 5em;
	height: 2em;
	display: block;
}

body#forgot_username form{
	margin-left: auto;
	margin-right: auto;
	margin-top: 60px;
	color: black;
}

body#create_account_form div#container{
	width: 50em;
	text-align: left;
	margin: 0 auto;
	line-height: 1.45;
}

body#create_account_form input[type=submit]{
	font-size: 110%;
}

body#create_account_form label{
	margin-left: 2em;
	font-size: 120%;
}

div#container div.wrapper h1{
	display: inline-block;
	color: #FFFFFF;
}

body#class_details div#scroll_form_edit_grade{
	top: -400px; /* start off the screen */
	width: 100%;
	position: fixed;
	z-index: 102;
	left: 0;
	margin: 0 0 0 0;
	display: block;
}

body#class_details div#scroll_form_add_category{
	top: -400px; /* start off the screen */
	width: 100%;
	position: fixed;
	z-index: 102;
	left: 0;
	margin: 0 0 0 0;
	display: block;
}

body#class_details div#scroll_form_add_assignment{
	top: -400px; /* start off the screen */
	position: fixed;
	width: 100%;
	z-index: 102;
	left: 0;
	margin: 0 0 0 0;
	display: block;
}

body#class_details div#scroll_form_edit_grade img#edit-hideBtn{
	cursor: pointer;
}

body#class_details div#scroll_form_add_assignment img#add-hideBtn{
	cursor: pointer;
}

body#class_details div#scroll_form_add_category img#cat-hideBtn{
	cursor: pointer;
}

body#class_details div#scroll_form_edit_grade form div.centered{
	width: 90%;
	margin: 30px auto;
}

body#class_details div#scroll_form_add_assignment form div.centered{
	width: 90%;
	margin: 30px auto;
}

body#class_details div#scroll_form_add_category form div.centered{
	width: 90%;
	margin: 30px auto;
}

a.edit-grade, a#addCat{
	cursor: pointer;
}

body#dashboard div#class_summary{
	border: #aaa solid 2px;
	line-height: 1.45em;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.8em 1.2em;
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	display: inline-block;
	vertical-align: top;
	width: 100%;
}

body#dashboard div#class_summary h2{
	color: #404040;
}

body#dashboard div#class_summary hr, body#dashboard div#point_summary hr{
	position: relative;
	top: -16px;
	padding: 0;
	margin: 0;
}

body#dashboard div#point_summary h2{
	color: #404040;
}

body#dashboard div#point_summary{
	border: #aaa solid 2px;
	line-height: 1.45em;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.8em 1.2em;
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	display: inline-block;
	vertical-align: top;
	width: 100%;
}

body#dashboard div#point_summary div.hide_cats{
	display: none;
}

body#dashboard div#point_summary div.pt_line_wrapper {
	font-size: 130%;
}

body#dashboard div#point_summary div.pt_class_line {
	display: inline-block;
	color: #17731D;
}

body#dashboard div#point_summary div.class_rewards {
	display: inline-block;
	float: right;
	color: #17731D;
}

body#dashboard div#point_summary div.pt_cat_line {
	margin-left: 0.75cm;
}

body#dashboard div#point_summary div.pt_cat_line div.pt_cat_name {
	display: inline-block;
}

body#dashboard div#point_summary div.pt_cat_line div.pt_cat {
	display: inline-block;
	float: right;
}

div.singleclass{
	font-size: 130%;
	display: inline-block;
	width: 100%;
}

div.singleclass div.class_name {
	display: inline-block;
}

div.singleclass div.class_points {
	display: inline;
	color: #17731D;
}

div.singleclass div.class_letter_grade {
	display: inline-block;
	margin-left: 20px;
	width: 30px;
	color: #17731D;
}

div#class_summary div.singleclass div.wrapper {
	display: inline;
	margin-left: auto;
	margin-right: 0px;
	float: right;
}

div.singleclass div.wrapper {
	display: inline-block;
}

body#class_details div#container p.category {
	margin-left: 30px;
}

body#class_details div#container p.assignment_grade {
	margin-left: 60px;
}

p.field_error {
	display: inline;
	color: #B4161A;
	margin-left: 14px;
	font-size: 130%;
}

p.field_good {
	display: inline;
	color: green;
	margin-left: 14px;
	font-size: 130%;
}

body#class_details div#page_content div#container div#categories_section {
	border: #aaa solid 2px;
	border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
	padding: 0.4em 1.2em;
	background: #83DBD1;
	text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #000 inset;
    -webkit-box-shadow: 1px 1px 2px #000 inset;
    box-shadow: 1px 1px 6px #fff inset;
	display: inline-block;
	vertical-align: top;
	width: 100%;
}

body#class_details div#page_content div#container div#categories_section p a{
	color: #17731D;
}

body#class_details div#page_content div#container div#categories_section img.insert-grade{
	cursor: pointer;
}

body#class_details div#page_content div#container iframe.category_details_window {
	padding: 0.4em 1.2em;
	width: 45%;
	height: 500px;
	display: inline-block;
	vertical-align: top;
	margin-right: 2%;
	float: right;
	scrolling: yes;
	border: 0px;
}

fieldset {
	/*font-size: 120%;
	font-weight: bolder;*/
	color: black;
    margin-top: 2em;
    border-radius: 6px 4px 2px 2px;
    -moz-border-radius: 6px 4px 2px 2px;
    -webkit-border-radius: 6px 4px 2px 2px;
    border: #aaa solid 2px;
    padding: 1.2em;
    background: #83DBD1;
    text-shadow: 2px 2px #000 inset;
    -moz-box-shadow: 10px 10px 2px #fff inset;
    -webkit-box-shadow: 1px 1px 2px #fff inset;
    box-shadow: 1px 1px 6px #fff inset;
}

legend {
	color: #033500;
	top: 12px;
	position: relative;
	font-size: 120%;
	font-weight: bolder;
}

div#container fieldset a{
	color: #6F5C19;
}

div#page_content fieldset a{
	color: #6F5C19;
}