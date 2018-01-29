<?php
/*
author: dwizzel
date: 29-01-2018
desc: load data into global var, then manipulates the global var with lodash instead of the table data
links:
    https://lodash.com/docs/4.17.4#orderBy
    https://github.com/jdalton/docdown/blob/0.5.0/lib/util.js#L13-L41
*/
?>
<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>global.css">
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>fontawesome-all.min.css">
  <!--<link rel="stylesheet" href="<?php echo CSS_PATH; ?>font-awesome.min">-->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="<?php echo JS_PATH; ?>lodash.min.js"></script>
</head>
<style>
</style>
<body>
    <div class="container-fluid">
        <?php require_once(VIEW_PATH.'menu.php'); ?>
        <h1><?php echo $data['title'];?></h1>
        <div class="table-responsive">
            <table class="clients table table-striped table-hover table-bordered">
            <thead class="thead-dark">
            </thead>
            <tbody>
                <tr class="controls">
                    <td colspan="4"></td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="add btn btn-primary small">add</button>
                        <button type="button" class="refresh btn btn-secondary small">refresh</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
</body>
<script>

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function textEntities(str) {
    return String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
}
  
jQuery(document).ready(function(){

    var gData = [];
    var gNewId = 0;
    
    var Row = {
        clientId: {
            editable:false, 
            class:'', 
            head:'#id'
            },
        firstname: {
            editable:true, 
            placeholder:"First Name", 
            default:'', 
            class:'editable', 
            head:'Firstname'
            },
        lastname: {
            editable:true, 
            placeholder:"Last Name", 
            default:'', 
            class:'editable', 
            head:'LastName'
            },
        appointmentDate: {
            editable:true, 
            placeholder:"Appointment Date", 
            default:'0000-00-00 00:00:00', 
            class:'editable',
            head:'Appointment Date'
            }
    };

    addHeader();
    getRows();

    $('BUTTON.refresh').click(function(){
        getRows();
    });
    $('BUTTON.add').click(function(){
        if($('TABLE.clients TR[rowid="' + gNewId + '"]').length){
            return;
        }
        $(this).attr('disabled', true);
        addRow({
            clientId: gNewId,
            firstname: '',
            lastname: '',
            appointmentDate: '0000-00-00 00:00:00'
        });
        modifyRow(gNewId);
    });

    function addHeader(){
        var html = '<tr>';
        for(var o in Row){
            html += '<th scope="col" colname="' + o + '">';
            html += '<label>' + Row[o].head + '</label>';
            html += '<div class="sorting"><i class="fas fa-angle-up" direction="asc"></i><i class="fas fa-angle-down" direction="desc"></i></div>';
            html += '</th>';
        }
        html += '<th scope="col"></th>';
        html += '</tr>';
        $('TABLE.clients THEAD')
            .html(html)
            .find('DIV.sorting i').each(function(){
                $(this).click(function(e){
                    sortData($(this).closest('TH').attr('colname'), $(this).attr('direction'));
                });
            });
            
    }

    function sortData(colname, direction){
        $('TABLE.clients TBODY TR[class!="controls"]').remove();
        var arr = _.orderBy(gData, [colname], [direction]);
        gData = [];
        arr.forEach(function(row){
            addRow(row);
            });
    }
    
    function getRows(){
        gData = [];
        disableAddAction(true);
        disableRefreshAction(true);
        $('TABLE.clients TBODY TR[class!="controls"]').remove();
        var post = 'GET';
        var url = 'http://basics.homestead.local/api/clients/';
        //var url = 'http://tracker.homestead.local/api/test/db/';
        $.ajax({
            type: post,
            url: url,
        }).done(function(data){
            data.forEach(function(row){
                addRow(row);
            });
            disableAddAction(false);
            disableRefreshAction(false);
        }).fail(function(xhr, status, error){
            ajaxFail(xhr, status, error);
        }); 
    }

    function ajaxFail(xhr, status, error){
        console.log('fail:' + status + '|' + error);
        console.log(xhr);
    }

    function addActions(id){
        var obj = $('TABLE.clients TBODY TR[rowid="' + id + '"]');
        obj.find('BUTTON.delete').click(function(e){
            deleteRow($(this).closest('TR').attr('rowid'));
        });
        obj.find('BUTTON.modify').click(function(e){
            modifyRow($(this).closest('TR').attr('rowid'));
        });
        obj.find('BUTTON.cancel').click(function(e){
            cancelRowModif($(this).closest('TR').attr('rowid'));
        });
        obj.find('BUTTON.save').click(function(e){
            saveRowModif($(this).closest('TR').attr('rowid'));
        });
    }

    function addRow(data){
        gData.push(data);
        var row = Row;
        var html = '<tr>';    
        for(var o in row){
            let tag = (o == 'clientId')? 'th':'td';
            let inner = ' colname="' + o + '" class="' + row[o].class + '"';
            html += '<' + tag + inner + '>';
            if(typeof data[o] == 'undefined'){
                html += row[o].default; 
            }else{
                html += htmlEntities(data[o]); 
            }
            html += '</' + tag + '>'; 
        }
        html += '<td class="actions">';
        html += '<div class="btn-group btn-group-sm edit" role="group">';
        html += '<button type="button" class="cancel btn btn-warning">cancel</button>';
        html += '<button type="button" class="save btn btn-success">save</button>';
        html += '</div>';
        html += '<div class="btn-group btn-group-sm data" role="group">';
        html += '<button type="button" class="modify btn">modify</button>';
        html += '<button type="button" class="delete btn btn-danger">delete</button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        if($('TABLE.clients TR.multi-controls').length == 1){
            $('TABLE.clients TR.multi-controls')
                .before(html)
                .prev()
                .attr('rowid', data.clientId);
        }else{
            $('TABLE.clients TBODY TR:last')
                .before(html)
                .prev()
                .attr('rowid', data.clientId);
        }
        addActions(data.clientId);
    }

    function setRowLoading(id){
        var obj = $('TABLE.clients TR[rowid="' + id + '"]');
        obj.addClass('saving');
        obj = obj.find('TD.actions');
        obj.find('BUTTON.save').html('<i class="fas fa-spinner fa-pulse"></i>');
        obj.find('BUTTON.save').attr('disabled', true);
        obj.find('BUTTON.cancel').attr('disabled', true);
        
    }

    function setRowErrorMsg(id, arr){
        var msg = '';
        for(var o in arr){
            if(typeof(arr[o].message) == 'string'){
                msg += ' ' + arr[o].message + ','; 
            }
        }
        if(msg != ''){
            msg = '<b>Error: </b>' + msg.substring(0, msg.length - 1);
            var html = '<th colspan="5"><span class="error">' + msg + '</span></th>';
            if($('TABLE.clients TR[rowid="' + id + '"]').prev().hasClass('errormsg')){
                $('TABLE.clients TR[rowid="' + id + '"]').prev().html(html);            
            }else{
                html = '<tr class="error errormsg">' + html + '</tr>';
                $('TABLE.clients TR[rowid="' + id + '"]').before(html);
            }
        }
    }

    function setRowError(id){
        var obj = $('TABLE.clients TR[rowid="' + id + '"]');
        obj.removeClass('saving');
        obj.addClass('error');
        var obj = obj.find('TD.actions');
        obj.find('BUTTON.save').html('save');
        obj.find('BUTTON.save').attr('disabled', false);
        obj.find('BUTTON.cancel').attr('disabled', false);
    }

    function setRowSaved(id){
        deleteErrorMsgRow(id);
        var obj  = $('TABLE.clients TR[rowid="' + id + '"]');
        var rowData = _.find(gData, {clientId: parseInt(id)});
        obj.removeClass('editing error saving');
        obj.find('TD.editable').each(function(index){
            rowData[$(this).attr('colname')] = $(this).find('input').val();
            $(this).html(htmlEntities(rowData[$(this).attr('colname')]));
            });
        obj = obj.find('TD.actions');
        obj.find('BUTTON.save').html('save');
        obj.find('BUTTON.save').attr('disabled', false);
        obj.find('BUTTON.cancel').attr('disabled', false);    
        checkMultiControls();
    }

    function setNewRowId(id){
        var obj  = $('TABLE.clients TR[rowid="' + gNewId + '"]');
        var rowData = _.find(gData, {clientId: parseInt(gNewId)});
        rowData.clientId = id;
        obj.find('TH:first').html(id);
        obj.attr('rowid', id);
    }

    function disableAddAction(enable){
        $('TABLE.clients TBODY TR.controls BUTTON.add').attr('disabled', enable);
    }

    function disableDeleteAction(id, enable){
        var obj = $('TABLE.clients TBODY TR[rowid="' + id + '"] TD.actions BUTTON.delete');
        obj.attr('disabled', enable);
        (!enable)? obj.html('delete') : obj.html('<i class="fas fa-spinner fa-pulse"></i>');
    }

    function disableRefreshAction(enable){
        var obj = $('TABLE.clients TBODY TR.controls BUTTON.refresh');
        obj.attr('disabled', enable);
        (!enable)? obj.html('refresh') : obj.html('<i class="fas fa-spinner fa-pulse"></i>');
    }

    function deleteErrorMsgRow(id){
        if($('TABLE.clients TR[rowid="' + id + '"]').prev().hasClass('errormsg')){
            $('TABLE.clients TR[rowid="' + id + '"]').prev().remove();
        }
    }

    function cancelRowModif(id){
        deleteErrorMsgRow(id);
        if(id == gNewId){
            disableAddAction(false);
            deleteTableRow(id);
        }else{
            var rowData = _.find(gData, {clientId: parseInt(id)});
            $('TABLE.clients TR[rowid="' + id + '"]')
                .removeClass('editing error saving')
                .find('TD.editable').each(function(index){
                    $(this).html(rowData[$(this).attr('colname')]);
                })
        }
        checkMultiControls();
    }

    function deleteRow(rowId){
        var post = 'DELETE';
        var url = 'http://basics.homestead.local/api/clients/' + rowId + '/';
        disableDeleteAction(rowId, true);
        $.ajax({
            type: post,
            url: url,
        }).done(function(data){
            deleteTableRow(rowId);
            checkMultiControls();
        }).fail(function(xhr, status, error){
            disableDeleteAction(rowId, false);
            ajaxFail(xhr, status, error);
        }); 
    }

    function deleteTableRow(id){
        _.pullAt(gData, _.findIndex(gData, {clientId: parseInt(id)}));
        $('.clients TR[rowid="' + id + '"]').remove();    
    }

    function saveRowModif(id){
        var values = {};
        $('TABLE.clients TR[rowid="' + id + '"]')
            .find('TD.editable')
            .each(function(index){
                values[$(this).attr('colname')] = $(this).find('input').val();
            });
        setRowLoading(id);
        var post = 'POST';
        var url = 'http://basics.homestead.local/api/clients/';
        if(id != gNewId){
            post = 'PUT';
            url = 'http://basics.homestead.local/api/clients/' + id + '/';
        } 
        $.ajax({
            type: post,
            url: url,
            data: values
        }).done(function(data){
            setRowSaved(id);    
            if(id == gNewId){
                disableAddAction(false);
                setNewRowId(data.clientId);
            }
            console.log('DONE');
        }).fail(function(xhr, status, error){
            ajaxFail(xhr, status, error);
            if(xhr.status == '400' && typeof(xhr.responseJSON) == 'object'){
                setRowErrorMsg(id, xhr.responseJSON);    
            }
            setRowError(id);
        });
    }

    function modifyRow(id){
        var rowData = _.find(gData, {clientId: parseInt(id)});
        $('.clients TR[rowid="' + id + '"]')
            .addClass('editing')
            .removeClass('error')
            .find('TD.editable').each(function(index){
                $(this).html('<input type="text">')
                    .find('input')
                    .attr('name', $(this).attr('colname'))
                    .val(textEntities(rowData[$(this).attr('colname')]));
            });
        checkMultiControls();
    }

    function cancelAllRowModif(){
        $('TABLE.clients TR.editing').each(function(index){
            cancelRowModif($(this).attr('rowid'));
        }); 
    }

    function saveAllRowModif(){
        var obj = $('TABLE.clients TR.editing');
        obj.each(function(index){
            saveRowModif($(this).attr('rowid'));
        }); 
    }

    function setCancelAllButt(){
        $('.clients TR.multi-controls BUTTON.cancel')
            .html('cancel all')
            .addClass('btn-warning')
            .off()
            .click(function(){
                cancelAllRowModif();
            });   
    }

    function setSaveAllButt(){
        $('.clients TR.multi-controls BUTTON.save') 
            .html('save all')
            .addClass('btn-success')
            .off()
            .click(function(){
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
        if($('TABLE.clients TR.multi-controls').length == 1){
            return;
        }
        var obj = $('TABLE.clients TR.controls');
        var html = '<tr class="multi-controls">';
        html += '<td colspan="4"></td>';
        html += '<td class="">';
        html += '<div class="btn-group btn-group-sm" role="group">';
        html += '<button type="button" class="cancel btn"></button>';
        html += '<button type="button" class="save btn"></button>';
        html += '</div>';
        html += '</td>';
        html += '</tr>';
        $('TABLE.clients TR.controls').before(html);
        setCancelAllButt();
        setSaveAllButt();

    }


});
</script>
</html>