<?php
require "config.php";
?>
<!doctype html>
<html lang="en">
<head>
    <title>Medical Diagnostic Tool</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
<!--    <link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>-->
<!--    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" />-->
    <link rel="stylesheet" href="css/jquery.multiselect.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        i{
            cursor: pointer;
        }
    </style>
    <script>
        var diffsymps =[];
        function getSelectedOptions(sel) {
            var opts = [],
                opt;
            var len = sel.options.length;
            for (var i = 0; i < len; i++) {
                opt = sel.options[i];

                if (opt.selected) {
                    opts.push(opt);
                    alert(opt.value);
                }
            }

            return opts;
        }


        function getsubloc(val) {
            $.ajax({
                type: "POST",
                url: "get_subloc.php",
                data:'sloc='+val,
                success: function(data){
                    $("#bodysubloc").html(data);
                    // alert('success')
                }
                /*error: function (a,b,c) {
                    console.log(c);
                }*/
            });
        }

        function getsymps(val) {
            $.ajax({
               type: "POST",
               url: "get_symps.php",
               // data: 'symps='+val,
               data:{symps:val},
               success: function (data) {
                   $("#bodysymps").html(data);
                   $('#bodysymps').multiselect( 'reload' );
                   $("#bodysymps").multiselect({
                       texts: {
                           placeholder: 'Select',
                           search: 'Search symptoms'
                       },
                       search : true
                   });

               }

            });

        }

        function getdiagnosis() {

            yob = $('#yob').val();
            gen = $("input[name='genradio']:checked").val();
            bloc = $("#bodyloc").val();
            bsloc = $("#bodysubloc").val();
            // symps = $("#bodysymps").val();
            symps = returnsymps();
            // alert(symps);
            $.ajax({
                type: "POST",
                url: "get_diag.php",
                data:{yob:yob,gen:gen,bloc:bloc,bsloc:bsloc,symps:symps},
                beforeSend: function() {
                    $("#animation").show();
                },
                success: function (data) {
                    $("#tablebody").html(data);
                    $("#animation").hide();

                }

            });
        }






    </script>
</head>
<body>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1>Medical Diagnostic Tool(Demo Site)</h1>
        <p>Find out what's making you sick.....</p>
    </div>

</div>
<div class="container-fluid">
    <div class="row">
        <div class="card" style="min-width: 25%;max-width: 25%;">
            <h4 class="card-header"> 1. Select Symptoms</h4>
            <form class="form-inline padit" action="index.php" method="post">

                <label for="yob" >Year of Birth:</label>
                <input class="form-control" type="number" value="1980" id="yob">
            </form>
            <form class="form-inline padit">
                <label>Gender:</label>
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="genradio" value="male" id="m" checked="checked">Male
                </label>
                <label class="form-check-label">
                    <input class="form-check-input" type="radio" name="genradio" value="female" id="f">Female
                </label>
            </form>
            <form class="form-group padit" method="post" action="">
                <label>Body Location:</label>
                <div id='response'></div>
                <?php
                $obj = new schecker();
                $blarray = $obj->getbodyloc();
//                $bslarray = $obj1->getbodysubloc();
//                print_r(sizeof($bslarray));
                ?>
                <select id="bodyloc" name="bloc"  class="form-control" onchange="getsubloc(this.value)"  >
<!--                    <option selected="selected" disabled>Choose One</option>-->
                <option selected="selected" disabled>Select</option>
                <?php
                    foreach ($blarray as $i){
                        ?>
                        <option value="<?php echo $i['ID'];?>"><?php echo $i['Name'];?></option>
                            <?php
                    }
                    ?>
                </select>

                <label>Sublocation:</label>
                <select id="bodysubloc" name="bsloc" onchange="getsymps(this.value);" class="form-control" >
<!--                    <option selected="selected" >Select</option>-->
                </select>
                <label>Symptoms:</label>
                <select id="bodysymps" name="bsymps" class="form-control" multiple >
                </select>

                <div style="text-align: center;margin-top: 50px">

                    <input type="button" value="SELECT" class="btn btn-primary" onclick=getselectedsymps()>
                    <input type="button" value="CLEAR ALL" class="btn btn-danger" onclick=clearsymptoms()>

                </div>


            </form>






        </div>
        <div class="card" style="min-width: 25%;max-width: 25%">
            <h4 class="card-header">2. Final Symptoms</h4>
            <table id="symptable" class="table table-bordered" style="text-align: center">
                <tr><th>NO SYMPTOMS SELECTED</th></tr>
            </table>
            <div style="text-align: center;margin-top: 50px">
                <input id="diagbutton" type="button" value="DIAGNOSE" class="btn btn-success" disabled="true" onclick=getdiagnosis()>
            </div>
            <div id="animation" style="text-align: center">
                <img src="30.gif">
            </div>
        </div>
        <div class="card" style="min-width: 50%;max-width: 50%">
            <h4 class="card-header">3. Possible Conditions</h4>
            <table id="diagtable" class="table table-bordered table-striped" style="text-align: center">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Chances</th>
                        <th>More Information</th>
                    </tr>
                </thead>
                <tbody id="tablebody">

                </tbody>
            </table>

        </div>

</div>
    <!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<!-- Include the plugin's CSS and JS: -->

<!--<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>-->
<!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
  <!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
  <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>-->
<!--<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>-->
<!--<script type="javascript" src="js/script.js"></script>-->

<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.multiselect.js"></script>
    <script src="js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
<!--<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>-->
<script>
    var allval=[];
    var diffsymps =[];
    $(function () {

        $("#bodysymps").multiselect({
            texts: {
                placeholder: 'Select Symptoms',
                search: 'Search symptoms'
            },
            search : true,
        });

    });

    function unique_jq(list) {
        var result = [];
        $.each(list, function(i, e) {
            if ($.inArray(e, result) == -1) result.push(e);
        });
        return result;
    }
    function getselectedsymps() {
        $('#diagbutton').attr('class','btn btn-success');
        $('#diagbutton').removeAttr('disabled');
        // var allval = [];
        var vals = $("#bodysymps option:selected").map(function() {
            return $(this).text();
        }).get();

        allval.push(vals);
        table = $('#symptable');
        $('#symptable tr').remove();
        var newallval = [];
        for(var i = 0; i < allval.length; i++)
        {
            newallval = newallval.concat(allval[i]);
        }

        newallval = unique_jq(newallval);
        for(var i=0;i<newallval.length;i++){
            table.append('<tr><td>'+newallval[i]+'</td></tr>');
        }
    }

    function returnsymps() {
        diffvals = $('#bodysymps').val();
        diffsymps.push(diffvals);
        var newdiffsymps = [];
        for(var i = 0; i < diffsymps.length; i++)
        {
            newdiffsymps = newdiffsymps.concat(diffsymps[i]);
        }

        newdiffsymps = unique_jq(newdiffsymps);
        // console.log(newdiffsymps);
        return newdiffsymps;
    }
    
    

    function getselectedblocs() {
        var allval = $('#bodyloc').val();
        return allval;
    }

    $('[data-toggle="popover"]').popover();
    function clearsymptoms() {
        $('#symptable tr').remove();
        $('#diagtable #tablebody tr').remove();
        $('#symptable').append('<tr><th>NO SYMPTOMS SELECTED</th></tr>');

        // $('#bodysymps').prop('selectedIndex',0);
        $('#bodyloc').prop('selectedIndex',0);
        $('#bodysubloc').prop('selectedIndex',0);
        $('#bodysymps').multiselect( 'reset' );
        getsubloc(this.value);
        getsymps(this.value);

        diffsymps=[];
        allval=[];
        // console.log(diffsymps);



    }
    var $loading = $('#animation').hide();
    /*var $loading = $('#animation').hide();
    $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });*/
</script>

</body>
</html>




