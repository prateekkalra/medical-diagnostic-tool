<?php
require 'config.php';
if(!empty($_POST["symps"])){

    $obj = new schecker();
    $tokenGenerator = new TokenGenerator($obj->config['username'], $obj->config['password'], $obj->config['authServiceUrl']);
    $token = $tokenGenerator->loadToken();
    if (!isset($token))
        exit();
    $diagnosisClient = new DiagnosisClient($token, $obj->config['healthServiceUrl'], 'en-gb');
    $sympsid = $_POST['symps'];

//    $bodySublocations = $diagnosisClient->loadBodySublocations($sympsid);
    $symptoms =$diagnosisClient->loadSublocationSymptoms($sympsid,'man');
    if (!isset($symptoms))
        exit();
    $sympsarray = array_values($symptoms);
    ?>
<!--    <option selected="selected" disabled>Select</option selected="selected">-->
    <?php
    foreach ($sympsarray as $i){
        ?>
        <option value="<?php echo $i['ID'];?>"><?php echo $i['Name'];?></option>
        <?php
    }
    ?>
    </select>
    <?php
}
?>