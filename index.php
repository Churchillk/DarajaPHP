<?php
if(isset($_POST['Deposit']))
{
//access token
  $consumerKey = "add your consumer key from daraja";
  $consumerSecret = "add your secrete from darraja";
  $access_token_url = 'add tocken url';//in api/authorizations
  $headers = ['Content-Type:application/json; charset=utf8'];
  $curl = curl_init($access_token_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($curl, CURLOPT_HEADER, FALSE);
  curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
  $result = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
  $result = json_decode($result);
  $access_token = $result->access_token;
  //echo $access_token; //uncomment to generate this in order to move
  curl_close($curl);
//end access token
  $amount = $_POST['amount'];
  $phone = $_POST['accountnumber'];
  //checking user input
  $f3d = substr($phone, 0, 3);
  if($f3d == '254')
  {
    $phone = $phone;
  }
  else
  {
    $phone = '254'.(int)$phone ;
  }
//start stk push
  date_default_timezone_set('Africa/Nairobi');
  $processrequestUrl = 'get your process url';
  $callbackurl = 'create a php call back in your server and pass the url here';
  $passkey = "add passkey";
  $BusinessShortCode = 'add business code';
  $Timestamp = date('YmdHis');
  // ENCRIPT  DATA TO GET PASSWORD
  $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
  //$phone = $accountnumber;//phone number to receive the stk push
  $money = $amount;
  $PartyA = $phone;//number to receive cash
  $PartyB = 'receiver number';
  $AccountReference = ''; //name to be printed when mpesa push popup
  $TransactionDesc = 'any phrace';
  $Amount = $money;
  $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
  //INITIATE CURL
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
  curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); //setting custom header
  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $callbackurl,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
  );

  $data_string = json_encode($curl_post_data); //getting checkout request id to be used in query.php
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  //if you want to see if there is a successfull push, echo below line
  $curl_response = curl_exec($curl);
  //ECHO  RESPONSE
  $data = json_decode($curl_response);
  $CheckoutRequestID = $data->CheckoutRequestID;
  $ResponseCode = $data->ResponseCode;
  if ($ResponseCode == "0") 
  {
    echo "<script>window.location.href='donation.php?success=please enter your mpesa pin';</script>";
  }
  else
  {
    echo "<script>window.location.href='donation.php?error=please try again later;</script>";
  }
}
?>
<!--HTML START HERE-->
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="refresh" content="1s">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="STYLE.CSS">
    <link rel="stylesheet" href="donate.css">
    <link rel="icon" type="images/x-icon" href="mainlogo.jpg" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Donate Now</title>
</head>
<body>
    <div class="father">
        <div class="f-main">
<!--about-->
<section class="about" id="about">
    <div class="about_main">
        <div class="row">
            <div class="col-75">
              <div class="container">
                <form action="#" method="POST">
                <?php
                if(isset($_GET["success"]))
                {
                  echo "<p style='color: green; 
                                  text-align: center;
                                  font-weight: bold;
                                  padding: 2%'>".$_GET["success"]."
                        </P>";
                }
                elseif(isset($_GET["error"]))
                {
                  echo "<p style='color: red; text-align: center'>".$_GET["error"]."</P>";
                }
                ?>
                  <div class="row">
                    <div class="col-50">
                      <h3>Lipa Na Mpesa</h3>
                      <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                      <input type="text" id="fname" name="firstname" placeholder="Black Sheep">
                      <label for="email"><i class="fa fa-envelope"></i> Email</label>
                      <input type="text" id="email" name="email" placeholder="blacksheep@gmail.com">
                      <label for="adr"><i class="fa fa-address-card-o"></i> Phone Number</label>
                      <input type="text" id="adr" name="accountnumber" placeholder="254712345678">
                      <label for="city"><i class="fa fa-institution"></i> Amount</label>
                      <input type="text" id="city" name="amount" placeholder="enter amount to donate">
                    </div>
          
                    <div class="col-50">
                      <h3>Payment via Bank</h3>
                      <label for="fname">Accepted Cards</label>
                      <div class="icon-container">
                        <i class="fa fa-cc-visa" style="color:navy;"></i>
                        <i class="fa fa-cc-amex" style="color:blue;"></i>
                        <i class="fa fa-cc-mastercard" style="color:red;"></i>
                        <i class="fa fa-cc-discover" style="color:orange;"></i>
                      </div>
                      <label for="cname">Name on Card</label>
                      <input type="text" id="cname" name="cardname" placeholder="Black Sheep">
                      <label for="ccnum">Credit card number</label>
                      <input type="text" id="ccnum" name="cardnumber" placeholder="1111-2222-3333-4444">
                      <label for="expmonth">Exp Month</label>
                      <input type="text" id="expmonth" name="expmonth" placeholder="September">
                      <div class="row">
                        <div class="col-50">
                          <label for="expyear">Exp Year</label>
                          <input type="text" id="expyear" name="expyear" placeholder="2018">
                        </div>
                        <div class="col-50">
                          <label for="cvv">CVV</label>
                          <input type="text" id="cvv" name="cvv" placeholder="352">
                        </div>
                      </div>
                    </div>
                    
                  </div>
                  <input type="submit" value="Continue to checkout" class="btn" name = "Deposit">
                </form>
              </div>
            </div>
</section>
        </div>
        <div id="footer">
            <div class="footerbox">
                <div class="footer3">
                    <h5>Service With Dignity</h5>
                    <div class="pra">
                        <p style="text-align: justify;">
                            help us today in helping those who cannot help themselves
                        </p>
                        <a href="index.html" style="color: green; text-decoration: none;"><button>home</button></a>
                    </div>
                </div><!--end footer 3-->
            </div><!--end footer box-->
        </div><!--end footer-->
<!--FOOTER-->
    </div>
<script src="HUM.JS"></script>
<script src="payment.js"></script>
<script src="paymentalert.js"></script>
</body>
</html>
<!--about us-->