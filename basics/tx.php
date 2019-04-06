<?php
$sender="ak_24jcHLTZQfsou7NvomRJ1hKEnjyNqbYSq2Az7DmyrAyUHPq8uR";
$receiver="ak_zUQikTiUMNxfKwuAfQVMPkaxdPsXP8uAxnfn6TkZKZCtmRcUD";
$amount=20000;
$payload='hello';
sendTx($sender,$receiver,$amount,$payload);


function sendTx($sender,$receiver,$amount,$payload){
  $url = "http://127.0.0.1:3113/v2/debug/transactions/spend";
  $jsonStr = '{"sender_id":"'.$sender.'", "recipient_id":"'.$receiver.'", "amount":'.$amount.', "fee":20000000000000, "ttl":60000, "payload":"'.$payload.'"}';
  $return= http_post_json($url, $jsonStr);    
  print_r($return);   
  $keys=json_decode($return[1]);
  
  $tx_unsigned=$keys->tx;

  $cmd='~/main account sign mywalletname "'.$tx_unsigned.'" --password mypasswd -n ae_mainnet';
  exec($cmd,$ret);
  $tmpstr=explode(" ",$ret[3]);
  $tx_signed= $tmpstr[count($tmpstr)-1];
  echo "tx_signed:$tx_signed\n";
  //print_r($ret);sleep(40);
  $url = "http://127.0.0.1:3013/v2/transactions";
  $jsonStr ='{ "tx": "'.$tx_signed.'"}';
  $return= http_post_json($url, $jsonStr); 
  print_r($return);
}

function http_post_json($url, $jsonStr)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        )
    );
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
 
    return array($httpCode, $response);
}
