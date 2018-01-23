<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <link rel="stylesheet" href="<?php echo CSS_PATH.'global.css'; ?>">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<style>
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
            $table .= '<tr clientid="'.$v['clientId'].'" class="data">';
            $table .= '<th rowindex="0" rowname="clientId" class="" scope="row">'.$v['clientId'].'</th>';
            $table .= '<td rowindex="1" rowname="firstname" class="editable">'.$v['firstname'].'</td>';
            $table .= '<td rowindex="2" rowname="lastname" class="editable">'.$v['lastname'].'</td>';
            $table .= '<td rowindex="3" rowname="appointmentDate" class="editable">'.$v['appointmentDate'].'</td>';
            $table .= '<td class="">';
            $table .= '<div class="btn-group btn-group-sm" role="group">';
            $table .= '<button type="button" class="modify btn">modify</button>';
            $table .= '<button type="button" class="delete btn btn-danger">delete</button>';
            $table .= '</div>';
            $table .= '</td>';
            $table .= '</tr>';
        }
        $table .= '<tr class="controls">';
        $table .= '<td colspan="4"></td>';
        $table .= '<td>';
        $table .= '<div class="btn-group btn-group-sm" role="group">';
        $table .= '<button type="button" class="add modify btn btn-primary">add</button>';
        $table .= '</div>';
        $table .= '</td>';
        $table .= '</tr>';
        $table .= '</tbody>';
        $table .= '</table>';
        $content .= '<div class="table-responsive">';
        $content .= $table;
        $content .= '</div>';
        //output 
        echo $content;
        ?>
    </div>
</body>
<script>

  
jQuery(document).ready(function(){

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
    
    function Row(){
        this.id = gNewId;
        this.modified = false;
        this.cols = [
            {editable:false, value:this.id, name:"clientId"},
            {editable:true, value:"", name:"firstname", placeholder:"First Name"},
            {editable:true, value:"", name:"lastname", placeholder:"Last Name"},
            {editable:true, value:"", name:"appointmentDate", placeholder:"Appointment Date"}
        ];
    }

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
        $('.clients TR[clientid="' + id + '"]').removeClass('editing error');
        $('BUTTON.add').attr('disabled', false);
        $('BUTTON.add').removeClass('disabled');
    }

    function cancelRowModif(id){
        if(id == gNewId){
            deleteTableRow(id);
        }else{
            var obj = $('.clients TR[clientid="' + id + '"]');
            var row = obj.data('row');
            obj.removeClass('editing');
            obj.find(' TD.editable').each(function(index){
                var rowindex = $(this).attr('rowindex');
                $(this).html(row.cols[rowindex].value);
            });
            setModifyButt(obj.find('.modify'), id);
            setDeleteButt(obj.find('.delete'), id);
        }
        checkMultiControls();
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
            checkMultiControls();
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
        var row = new Row();
        var html = '<tr clientid="' + row.id + '" class="data">';    
        var str = '';
        for(var i=0; i<row.cols.length; i++){
            let inner = ' rowindex="' + i + '" rowname="' + row.cols[i].name + '"';
            if(row.cols[i].editable){
                inner += ' class="editable"';
            }
            let tag = (i == 0)? 'th':'td';
            str += '<' + tag + inner + '>' + row.cols[i].value + '</' + tag + '>'; 
        }
        html += str;
        html += '<td class="">';
        html += '<div class="btn-group btn-group-sm" role="group">';
        html += '<button type="button" class="modify btn">modify</button>';
        html += '<button type="button" class="delete btn btn-danger">delete</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        //var obj = $('.clients TR:last');
        var obj = $('.clients TR.multi-controls');
        if(!obj.length){
            obj = $('.clients TR.controls');
        }
        obj.before(html);
        $('.clients TR[clientid="' + gNewId + '"]').data('row', row);
        modifyRow(gNewId);
    }

    function saveRowModif(clientId){
        var obj = $('.clients TR[clientid="' + clientId + '"]');
        var row = obj.data('row');
        var data = {};
        obj.find('TD.editable').each(function(index){
            var rowindex = $(this).attr('rowindex');
            row.cols[rowindex].value = $(this).find('input').val();
            $(this).html(row.cols[rowindex].value);
            data[row.cols[rowindex].name] = row.cols[rowindex].value;
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
            data: data
        }).done(function(data){
            //console.log('done[' + this.clientId + ']:' + data);
            obj.find('TH[rowname="clientId"]').html(data.clientId);
            setModifyButt(obj.find('.modify'), data.clientId);
            setDeleteButt(obj.find('.delete'), data.clientId);    
            removeLoading(this.clientId);
            checkMultiControls();
            obj.attr('clientId', data.clientId);
        }).fail(function(xhr, status, error){
            //console.log('fail[' + this.clientId + ']:' + status + '|' + error);
            //make the row errors
            obj.addClass('error');
        });
    }

    function modifyRow(id){
        var obj = $('.clients TR[clientid="' + id + '"]');
        var row = obj.data('row');
        if(typeof (row) == 'undefined'){
            row = new Row();
            row.id = id;
            row.cols[0].value = id;
        }
        obj.addClass('editing');
        obj.find('TD.editable').each(function(index){
            var rowindex = $(this).attr('rowindex');
            row.cols[rowindex].value = $(this).html();
            $(this).html('<input type="text">');
            $(this).find('input').attr('name', row.cols[rowindex].name);
            $(this).find('input').attr('value', row.cols[rowindex].value);
            $(this).find('input').attr('placeholder', row.cols[rowindex].placeholder);
        });
        obj.data('row', row);
        setCancelButt(obj.find('.modify'), id);
        setSaveButt(obj.find('.delete'), id);
        checkMultiControls();
    }

    function cancelAllRowModif(){
        var obj = $('.clients TR.editing');
        obj.each(function(index){
            cancelRowModif($(this).attr('clientid'));
        }); 
    }

    function saveAllRowModif(){
        var obj = $('.clients TR.editing');
        obj.each(function(index){
            saveRowModif($(this).attr('clientid'));
        }); 
    }

    function setCancelAllButt(){
        var obj = $('.clients TR.multi-controls BUTTON.cancel'); 
        obj.html('all');
        obj.addClass('btn-warning');
        obj.unbind();
        obj.click(function(){
            cancelAllRowModif();
        });   
    }

    function setSaveAllButt(){
        var obj = $('.clients TR.multi-controls BUTTON.save'); 
        obj.html('all');
        obj.addClass('btn-success');
        obj.unbind();
        obj.click(function(){
            saveAllRowModif();
        });   
    }

    function checkMultiControls(){
        var obj = $('.clients TR.editing');
        if(obj.length > 1){
            addMultiControls();
        }else{
            $('.clients TR.multi-controls').remove();
        }
        
    }

    function addMultiControls(){
        if($('.clients TR.multi-controls').length == 1){
            return;
        }
        var obj = $('.clients TR.controls');
        var html = '<tr class="multi-controls">';
        html += '<td colspan="4"></td>';
        html += '<td class="">';
        html += '<div class="btn-group btn-group-sm" role="group">';
        html += '<button type="button" class="cancel btn"></button>';
        html += '<button type="button" class="save btn"></button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        obj.before(html);
        setCancelAllButt();
        setSaveAllButt();

    }

});
</script>
</html>