<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <link rel="stylesheet" href="<?php echo CSS_PATH.'global.css'; ?>">
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <?php
        //top menu
        require_once(VIEW_PATH.'menu.php');
        //content
        $content = '<h1>'.$data['title'].'</h1>';
        //data
        $table = '<table class="clients">';
        $table .= '<tr>';
        $table .= '<th>id</th>';
        $table .= '<th>firstname</th>';
        $table .= '<th>lastname</th>';
        $table .= '<th colspan="2">appointmentDate</th>';
        $table .= '</tr>';
        foreach($data['clients'] as $k=>$v){
            $table .= '<tr clientid="'.$v['clientId'].'">';
            $table .= '<td rowname="clientId">'.$v['clientId'].'</td>';
            $table .= '<td rowname="firstname" class="editable">'.$v['firstname'].'</td>';
            $table .= '<td rowname="lastname" class="editable">'.$v['lastname'].'</td>';
            $table .= '<td rowname="appointmentDate" class="editable">'.$v['appointmentDate'].'</td>';
            $table .= '<td class="control">';
            $table .= '<div><button class="modify">modify</button></div>';
            $table .= '<div><button class="delete">delete</button></div>';
            $table .= '</td>';
            $table .= '</tr>';
        }
        $table .= '<tr>';
        $table .= '<td colspan="4"></td>';
        $table .= '<td class="control"><div><button class="add">add</button></div></td>';
        $table .= '</tr>';
        $table .= '</table>';
        $content .= $table;
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
        obj.removeClass('save disabled');
        obj.attr('disabled', false);
        obj.unbind();
        obj.click(function(){
            deleteRow(id);
        });
    }
    
    function setModifyButt(obj, id){
        obj.html('modify');
        obj.removeClass('cancel disabled');
        obj.attr('disabled', false);
        obj.unbind();
        obj.click(function(){
            modifyRow(id);
        });
    }

    function setSaveButt(obj, id){
        obj.html('save');
        obj.addClass('save');
        obj.unbind();
        obj.click(function(){
            saveRowModif(id);
        });
    }

    function setCancelButt(obj, id){
        obj.html('cancel');
        obj.addClass('cancel');
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
        html += '<td rowname="clientId"></td>';
        html += '<td rowname="firstname" class="editable"></td>';
        html += '<td rowname="lastname" class="editable"></td>';
        html += '<td rowname="appointmentDate" class="editable"></td>';
        html += '<td class="control">';
        html += '<div><button class="modify">modify</button></div>';
        html += '<div><button class="delete">delete</button></div>';
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
            obj.find('TD[rowname="clientId"]').html(data.clientId);
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