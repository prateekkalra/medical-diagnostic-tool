<?php
require 'config.php';
if(!empty($_POST["sloc"])){

    $obj = new schecker();
    $tokenGenerator = new TokenGenerator($obj->config['username'], $obj->config['password'], $obj->config['authServiceUrl']);
    $token = $tokenGenerator->loadToken();
    if (!isset($token))
        exit();
    $diagnosisClient = new DiagnosisClient($token, $obj->config['healthServiceUrl'], 'en-gb');
    $slocid = $_POST['sloc'];
    $bodySublocations = $diagnosisClient->loadBodySublocations($slocid);
    if (!isset($bodySublocations))
        exit();
    $bodysublocarray = array_values($bodySublocations);
    ?>
    <option selected="selected" disabled>Select</option>
                <?php
                    foreach ($bodysublocarray as $i){
                        ?>
                        <option value="<?php echo $i['ID'];?>"><?php echo $i['Name'];?></option>
                            <?php
                    }
                    ?>
                </select>
<?php
}
?>