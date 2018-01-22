<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <!--<link rel="stylesheet" href="<?php echo CSS_PATH.'global.css'; ?>">-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<style>
    .clients TR.editing{
        background-color: #007bff17 !important;
    }
    .clients TR.editing TD, .clients TR.editing TH{
    }
    .clients TD.editable INPUT{
        width:100%;
        border:0;
        background-color:transparent;
        color: #00f;
        padding: 0;
        margin: 0;
    }
    .clients BUTTON{
        width: 70px;
    }
    .clients BUTTON.add{
        width: 140px;
    }
    .clients TH:last-child, .clients TH:first-child{
        width: 5%;
    }
    .clients TH{
        width: 30%;
    }

    
</style>
<body>
    <div class="container-fluid">
        <?php
        //top menu
        require_once(VIEW_PATH.'menu.php');
        //content
        $content = '<h1>'.$data['title'].'</h1>';
        //data
        $table = '<table class="clients table table-striped table-hover table-bordered">';
        $table .= '<thead class="thead-dark">';
        $table .= '<tr>';
        $table .= '<th scope="col">id</th>';
        $table .= '<th scope="col">firstname</th>';
        $table .= '<th scope="col">lastname</th>';
        $table .= '<th scope="col">appointmentDate</th>';
        $table .= '<th scope="col">actions</th>';
        $table .= '</tr>';
        $table .= '</thead>';
        $table .= '<tbody>';
        foreach($data['clients'] as $k=>$v){
            $table .= '<tr clientid="'.$v['clientId'].'" class="">';
            $table .= '<th rowname="clientId" class="" scope="row">'.$v['clientId'].'</th>';
            $table .= '<td rowname="firstname" class="editable">'.$v['firstname'].'</td>';
            $table .= '<td rowname="lastname" class="editable">'.$v['lastname'].'</td>';
            $table .= '<td rowname="appointmentDate" class="editable">'.$v['appointmentDate'].'</td>';
            $table .= '<td class="">';
            $table .= '<div class="btn-group btn-group-sm" role="group">';
            $table .= '<button type="button" class="modify btn">modify</button>';
            $table .= '<button type="button" class="delete btn btn-danger">delete</button>';
            $table .= '</div>';
            $table .= '</td>';
            $table .= '</tr>';
        }
        $table .= '<tr>';
        $table .= '<td colspan="4"></td>';
        $table .= '<td>';
        $table .= '<div class="btn-group btn-group-sm" role="group">';
        $table .= '<button type="button" class="add modify btn btn-primary">add</button>';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';
        $table .= '</tbody>';
        $table .= '</table>';
        //$content .= '<div class="row">';
        $content .= '<div class="table-responsive">';
        $content .= $table;
        $content .= '</div>';
        //$content .= '</div>';
        //output 
        echo $content;
        ?>
    </div>
</body>
<script>

  
jQuery(document).ready(function(){

    var gClient = {};
    var gNewId = 'new';

    $('.modify').click(function(){
        modifyRow($(this).closest('tr').attr('clientid'));
    });
    $('.delete').click(function(){
        deleteRow($(this).closest('tr').attr('clientid'));
    });
    $('BUTTON.add').click(function(){
        addRow();
    });

    function setDeleteButt(obj, id){
        obj.html('delete');
        obj.removeClass('btn-success');
        obj.addClass('btn-danger');
        obj.attr('disabled', false);
        obj.unbind();
        obj.click(function(){
            deleteRow(id);
        });
    }
    
    function setModifyButt(obj, id){
        obj.html('modify');
        obj.removeClass('btn-warning');
        obj.attr('disabled', false);
        obj.unbind();
        obj.click(function(){
            modifyRow(id);
        });
    }

    function setSaveButt(obj, id){
        obj.html('save');
        obj.removeClass('btn-danger');
        obj.addClass('btn-success');
        obj.unbind();
        obj.click(function(){
            saveRowModif(id);
        });
    }

    function setCancelButt(obj, id){
        obj.html('cancel');
        obj.addClass('btn-warning');
        obj.unbind();
        obj.click(function(){
            cancelRowModif(id);
        });
    }

    function setLoading(id){
        var obj = $('.clients TR[clientid="' + id + '"]');
        var modify = obj.find('TD.control').find('BUTTON.modify');
        modify.attr('disabled', true);
        modify.addClass('disabled');
        var del = obj.find('TD.control').find('BUTTON.delete');     
        del.attr('disabled', true);
        del.addClass('disabled');
        var add = $('BUTTON.add');
        add.attr('disabled', true);
        add.addClass('disabled');
    }

    function removeLoading(id){
        $('.clients TR[clientid="' + id + '"]').removeClass('editing');
        $('BUTTON.add').attr('disabled', false);
        $('BUTTON.add').removeClass('disabled');
    }

    function cancelRowModif(id){
        var obj = $('.clients TR[clientid="' + id + '"]');
        if(id == gNewId){
            deleteTableRow(id);
        }
        obj.removeClass('editing');
        obj.find(' TD.editable').each(function(index){
            var rowname = $(this).attr('rowname');
            $(this).removeClass('input');
            $(this).html(gClient[rowname]);
        });
        setModifyButt(obj.find('.modify'), id);
        setDeleteButt(obj.find('.delete'), id);
    }

    function deleteRow(clientId){
        var post = 'DELETE';
        var url = 'http://basics.homestead.local/api/clients/' + clientId + '/';
        $.ajax({
            clientId: clientId,
            type: post,
            url: url,
        }).done(function(data){
            removeLoading(this.clientId);
            deleteTableRow(this.clientId);
        }).fail(function(xhr, status, error){
            console.log('fail[' + this.clientId + ']:' + status);
        }); 
    }

    function deleteTableRow(id){
        $('.clients TR[clientid="' + id + '"]').remove();    
    }
    
    function addRow(){
        if($('.clients TR[clientid="' + gNewId + '"]').length != 0){
            return;
            }
        var obj = $('.clients TR:last');
        var html = '<tr clientid="' + gNewId + '">';
        html += '<th rowname="clientId">' + gNewId + '</th>';
        html += '<td rowname="firstname" class="editable"></td>';
        html += '<td rowname="lastname" class="editable"></td>';
        html += '<td rowname="appointmentDate" class="editable"></td>';
        html += '<td class="">';
        html += '<div class="btn-group btn-group-sm" role="group">';
        html += '<button type="button" class="modify btn">modify</button>';
        html += '<button type="button" class="delete btn btn-danger">delete</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        obj.before(html);
        modifyRow(gNewId);
    }

    function saveRowModif(clientId){
        gClient = {};
        var obj = $('.clients TR[clientid="' + clientId + '"]');
        obj.find('TD.editable').each(function(index){
            var rowname = $(this).attr('rowname');
            var value = $(this).find('input').val();
            gClient[rowname] = value;
            $(this).removeClass('input');
            $(this).html(value);
            setModifyButt(obj.find('.modify'), clientId);
            setDeleteButt(obj.find('.delete'), clientId);
            setLoading(clientId);
        });
        var post = 'POST';
        var url = 'http://basics.homestead.local/api/clients/';
        if(clientId != gNewId){
            post = 'PUT';
            url = 'http://basics.homestead.local/api/clients/' + clientId + '/';
        } 
        $.ajax({
            clientId: clientId,
            type: post,
            url: url,
            data: gClient
        }).done(function(data){
            obj.find('TH[rowname="clientId"]').html(data.clientId);
            setModifyButt(obj.find('.modify'), data.clientId);
            setDeleteButt(obj.find('.delete'), data.clientId);    
            removeLoading(this.clientId);
            obj.attr('clientId', data.clientId);
        }).fail(function(xhr, status, error){
            console.log('fail[' + this.clientId + ']:' + status);
        });
    }

    function modifyRow(id){
        var obj = $('.clients TR[clientid="' + id + '"]');
        obj.addClass('editing');
        obj.find('TD.editable').each(function(index){
            var rowname = $(this).attr('rowname');
            var value = $(this).html();
            gClient[rowname] = value;
            $(this).addClass('input');
            $(this).html('<input type="text">');
            $(this).find('input').attr('name', rowname);
            $(this).find('input').attr('value', value);
        });
        setCancelButt(obj.find('.modify'), id);
        setSaveButt(obj.find('.delete'), id);
    }

});
</script>
</html>