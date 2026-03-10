<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{!! strip_tags($title) !!}</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Inter', 'Arial', sans-serif;
			margin: 0;
			padding: 0;
			background-color: #f8f9fa;
			color: #333333;
			line-height: 1.6;
		}

		.container {
			max-width: 600px;
			margin: 20px auto;
			background: #ffffff;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
		}

		.header {
			background-color: #213861;
			padding: 30px;
			text-align: center;
		}

		.header img {
			max-width: 180px;
			height: auto;
		}

		.content {
			padding: 40px 30px;
		}

		.title {
			font-size: 24px;
			font-weight: 700;
			margin-top: 0;
			margin-bottom: 20px;
			color: #213861;
			text-align: center;
		}

		.message {
			font-size: 16px;
			color: #555555;
			margin-bottom: 30px;
		}

		.footer {
			background-color: #f1f4f9;
			padding: 25px;
			text-align: center;
			font-size: 14px;
			color: #777777;
		}

		.footer .social-links {
			margin-bottom: 15px;
		}

		.footer .social-links a {
			margin: 0 10px;
			text-decoration: none;
			color: #213861;
		}

		.btn {
			display: inline-block;
			padding: 12px 25px;
			background-color: #213861;
			color: #ffffff !important;
			text-decoration: none;
			border-radius: 6px;
			font-weight: 600;
			margin-top: 10px;
		}

		.customer-card {
			background: #f1f4f9;
			padding: 20px;
			border-radius: 8px;
			border-left: 4px solid #213861;
			margin: 20px 0;
		}

		[dir="rtl"] .customer-card {
			border-left: 0;
			border-right: 4px solid #213861;
		}

		.customer-card-title {
			font-weight: 700;
			margin-bottom: 10px;
			color: #213861;
			display: block;
		}

		.customer-card-item {
			display: block;
			margin-bottom: 5px;
		}
	</style>
</head>

<body>
	<div class="container">
		<!-- Brand Header -->
		<div class="header">
			<!-- Replace with actual logo URL if available -->
			<img src="{{ asset('website/client/image/icon2.png') }}"
				onerror="this.src='https://icons.iconarchive.com/icons/custom-icon-design/pretty-office-11/256/shop-icon.png'"
				alt="Clean Station">
		</div>

		<!-- Main Content -->
		<div class="content">
			<h1 class="title">{!! $title !!}</h1>
			<div class="message">
				{!! $msg !!}
			</div>
		</div>

		<!-- Professional Footer -->
		<div class="footer">
			<div class="social-links">
				<a href="#">Facebook</a>
				<a href="#">Twitter</a>
				<a href="#">Instagram</a>
			</div>
			<p>&copy; {{ date('Y') }} Clean Station. {{ trans('client.all_rights_reserved') ?? 'All rights reserved.' }}</p>
			<p>Riyadh, Saudi Arabia</p>
		</div>
	</div>
</body>

</html>