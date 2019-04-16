<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('others.company_name')}}</title>

    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link rel="shortcut icon" href="{{asset('assets/img/icon.png')}}" type="image/x-icon" width="50%"/>

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

    <style type="text/css">
    	* {
    	  margin: 0;
    	  padding: 0;
    	}

    	a {
    	  text-decoration: none;
    	}

    	body {
    	  font-weight: 600;
    	  color: #343434;
    	}

    	.error_section {
    	  display: flex;
    	  justify-content: center;
    	  align-items: center;
    	  flex-direction: column;
    	  height: 100vh;
    	  background-image: linear-gradient(-225deg, #1A1A1A, #343434);
    	}
    	.error_section_subtitle {
    	  color: #25F193;
    	  text-transform: uppercase;
    	  letter-spacing: 5pt;
    	  font-weight: 500;
    	  font-size: 0.8rem;
    	  margin-bottom: -5em;
    	}
    	.error_section .error_title {
    	  --x-shadow: 0;
    	  --y-shadow: 0;
    	  --x:50%;
    	  --y:50%;
    	  font-size: 15rem;
    	  transition: all 0.2s ease;
    	  position: relative;
    	  padding: 2rem;
    	}
    	.error_section .error_title:hover {
    	  transition: all 0.2s ease;
    	  text-shadow: var(--x-shadow) var(--y-shadow) 10px #1A1A1A;
    	}
    	.error_section .error_title p {
    	  position: absolute;
    	  top: 2rem;
    	  left: 2rem;
    	  background-image: radial-gradient(circle closest-side, rgba(255, 255, 255, 0.05), transparent);
    	  background-position: var(--x) var(--y);
    	  background-repeat: no-repeat;
    	  text-shadow: none;
    	  -webkit-background-clip: text;
    	  -webkit-text-fill-color: transparent;
    	  transition: all 0.1s ease;
    	}

    	.btn {
    	  padding: 0.8em 1.5em;
    	  border-radius: 99999px;
    	  background-image: linear-gradient(to top, #03A9F4, #00BCD4);
    	  box-shadow: 0px 2px 5px 0px rgba(0, 0, 0, 0.2), inset 0px -2px 5px 0px rgba(0, 0, 0, 0.2);
    	  border: none;
    	  cursor: pointer;
    	  text-shadow: 0px 1px #343434;
    	  color: white;
    	  text-transform: uppercase;
    	  letter-spacing: 1.5pt;
    	  font-size: 0.8rem;
    	  font-weight: 700;
    	  transition: ease-out 0.2s all;
    	}
    	.btn:hover {
    	  text-shadow: 0px 1px 1px #ffffff;
    	  transform: translateY(-5px);
    	  box-shadow: 0px 4px 15px 2px rgba(0, 0, 0, 0.1), inset 0px -3px 7px 0px rgba(0, 0, 0, 0.2);
    	  transition: ease-out 0.2s all;
    	}

    </style>
</head>
<body>


	<section class="error_section">
      <p class="error_section_subtitle">Opps Page is not available !</p>
      <h1 class="error_title">
        <p>401</p>
        401
      </h1>
      <a href="#" class="btn">Back to home</a>
    </section>
   
   <script type="text/javascript">
   	
   	 
   	const title = document.querySelector('.error_title')


   	//////// Light //////////
   	document.onmousemove = function(e) {
   	  let x = e.pageX - window.innerWidth/2;
   	  let y = e.pageY - window.innerHeight/2;
   	  
   	  title.style.setProperty('--x', x + 'px')
   	  title.style.setProperty('--y', y + 'px')
   	}

   	////////////// Shadow ///////////////////
   	title.onmousemove = function(e) {
   	  let x = e.pageX - window.innerWidth/2;
   	  let y = e.pageY - window.innerHeight/2;

   	  let rad = Math.atan2(y, x).toFixed(2); 
   	  let length = Math.round(Math.sqrt((Math.pow(x,2))+(Math.pow(y,2)))/10); 

   	  let x_shadow = Math.round(length * Math.cos(rad));
   	  let y_shadow = Math.round(length * Math.sin(rad));

   	  title.style.setProperty('--x-shadow', - x_shadow + 'px')
   	  title.style.setProperty('--y-shadow', - y_shadow + 'px')

   	}
   </script>

    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</body>
</html>




