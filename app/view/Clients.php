<!DOCTYPE html>
<html lang="<?php echo $data['lang']; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo $data['title']; ?></title>
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>global.css">
  <link rel="stylesheet" href="<?php echo CSS_PATH; ?>fontawesome-all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">firstname</th>
                    <th scope="col">lastname</th>
                    <th scope="col">appointmentDate</th>
                    <th scope="col">actions</th>
                </tr>
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

  
jQuery(document).ready(function(){

    getRows();

    var gNewId = 'new';
    
    var Row = function(){
        this.cols = {
            clientId: {editable:false, class:''},
            firstname: {editable:true, placeholder:"First Name", default:'', class:'editable'},
            lastname: {editable:true, placeholder:"Last Name", default:'', class:'editable'},
            appointmentDate: {editable:true, placeholder:"Appointment Date", default:'0000-00-00 00:00:00', class:'editable'}
        };
    }

    $('BUTTON.refresh').click(function(){
        getRows();
    });
    $('BUTTON.add').click(function(){
        if($('TABLE.clients TR[rowid="' + gNewId + '"]').length){
            return;
        }
        $(this).attr('disabled', true);
        addRow({clientId:gNewId});
        modifyRow(gNewId);
    });
    
    function getRows(){
        disableAddAction(true);
        disableRefreshAction(true);
        $('TABLE.clients TBODY TR[class!="controls"]').remove();
        var post = 'GET';
        var url = 'http://basics.homestead.local/api/clients/';
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
        console.log('fail:' + xhr + '|' + status + '|' + error);
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
        var row = new Row();
        var html = '<tr>';    
        var colIndex = 0;
        var values = {};
        for(var o in row.cols){
            let tag = (colIndex == 0)? 'th':'td';
            let inner = ' colindex="' + (colIndex++) + '" rowname="' + o + '" class="' + row.cols[o].class + '"';
            html += '<' + tag + inner + '>';
            if(typeof data[o] == 'undefined'){
                html += row.cols[o].default; 
                values[o] = row.cols[o].default;
            }else{
                html += data[o]; 
                values[o] = data[o];
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
        $('TABLE.clients TBODY TR:last')
            .before(html)
            .prev()
            .attr('rowid', data.clientId)
            .data('values', values);
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
        var obj  = $('TABLE.clients TR[rowid="' + id + '"]');
        var data = obj.data('values');
        obj.removeClass('editing error saving');
        obj.find('TD.editable')
            .each(function(index){
                data[$(this).attr('rowname')] = $(this).find('input').val();
                $(this).html(data[$(this).attr('rowname')]);
            });
        obj.data('values', data);    
        obj = obj.find('TD.actions');
        obj.find('BUTTON.save').html('save');
        obj.find('BUTTON.save').attr('disabled', false);
        obj.find('BUTTON.cancel').attr('disabled', false);    
        checkMultiControls();
    }

    function setNewRowId(id){
        var obj  = $('TABLE.clients TR[rowid="' + gNewId + '"]');
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

    function cancelRowModif(id){
        if(id == gNewId){
            disableAddAction(false);
            deleteTableRow(id);
        }else{
            $('TABLE.clients TR[rowid="' + id + '"]')
                .removeClass('editing error saving')
                .find('TD.editable').each(function(index){
                    $(this).html($(this).closest('tr').data('values')[$(this).attr('rowname')]);
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
        $('.clients TR[rowid="' + id + '"]').remove();    
    }

    function saveRowModif(id){
        var values = {};
        $('TABLE.clients TR[rowid="' + id + '"]')
            .find('TD.editable')
            .each(function(index){
                values[$(this).attr('rowname')] = $(this).find('input').val();
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
        }).fail(function(xhr, status, error){
            ajaxFail(xhr, status, error);
            setRowError(id);
        });
    }

    function modifyRow(id){
        $('.clients TR[rowid="' + id + '"]')
            .addClass('editing')
            .removeClass('error')
            .find('TD.editable').each(function(index){
                $(this).html('<input type="text">')
                    .find('input')
                    .attr('name', $(this).attr('rowname'))
                    .val($(this).closest('tr').data('values')[$(this).attr('rowname')]);
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