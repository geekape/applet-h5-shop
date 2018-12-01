<?php
$str = 'https%3a%2f%2feform.adsale.com.hk%2fvreg%2fPreregSubmitCloud%2fPreregSubmitPay%3fLangId%3d936%26ShowCode%3dCPS19';
echo ($str . "<br/>");
$url = urlencode('https://eform.adsale.com.hk/vreg/PreregSubmitCloud/PreregSubmitPay?LangId=936&ShowCode=CPS19');
echo ($url . "<br/>");
$re = str_replace([
    '/',
    '?',
    '&'
], [
    '@_',
    '@$',
    '@@'
], 'https://eform.adsale.com.hk/vreg/PreregSubmitCloud/PreregSubmitPay?LangId=936&ShowCode=CPS19');
echo $re;
exit();
phpinfo();
