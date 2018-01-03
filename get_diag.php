<?php
require 'config.php';
if(!empty($_POST["yob"])&&!empty($_POST["gen"])&&!empty($_POST["bloc"])&&!empty($_POST["bsloc"])&&!empty($_POST["symps"])){

    $obj = new schecker();
    if (!$obj->checkRequiredParameters())
        return;
    $tokenGenerator = new TokenGenerator($obj->config['username'], $obj->config['password'], $obj->config['authServiceUrl']);
    $token = $tokenGenerator->loadToken();
    if (!isset($token))
        exit();
    $obj->diagnosisClient = new DiagnosisClient($token, $obj->config['healthServiceUrl'], 'en-gb');
    $gen = $_POST["gen"];
    $yob = $_POST["yob"];
    $symps = $_POST["symps"];
    $selectedSymptoms = array_values($symps);

    $diagnosis = $obj->diagnosisClient->loadDiagnosis($selectedSymptoms,'male',1980);
    $darray = array_values($diagnosis);

    for($i=0;$i<sizeof($darray);$i++){
        $issueInfo = $obj->diagnosisClient->loadIssueInfo($darray[$i]['Issue']['ID']);
        if (!isset($issueInfo))
            exit();
        $pssympsarray = explode(',', $issueInfo['PossibleSymptoms']);

        ?>
        <tr>
            <td><?php print_r($darray[$i]['Issue']['Name']); ?></td>
            <td><?php print_r($darray[$i]['Issue']['Accuracy'].'%'); ?></td>
            <td>
                <a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="left"  title="Possible symptoms"data-content="<?php
                for($j=0;$j<sizeof($pssympsarray);$j++){
                    echo $pssympsarray[$j]."<br>";
                }
                ?>" data-html="true">
                    <i class="fa fa-cogs fa-2x"></i>
                </a>
                <a tabindex="0" role="button" data-placement="top" data-trigger="focus" data-toggle="popover"  title="Description"data-content="<?php
                echo $issueInfo['Description'];
                ?>" data-html="true">
                    <i class="fa fa-file-text fa-2x"></i>
                </a><a tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="right"  title="Treatment"data-content="<?php
                echo $issueInfo['TreatmentDescription'];
                ?>" data-html="true">
                    <i class="fa fa fa-hospital-o fa-2x"></i>
                </a>
            </td>

<!--            <td><a href="">More info</a></td>-->
        </tr>
        <?php
    }




}
?>
<script>
    $('[data-toggle="popover"]').popover();
</script>
