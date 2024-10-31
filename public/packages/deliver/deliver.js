function trans_deliver(order_id, doc_id){
    Dcat.confirm('Are you sure you want to deliver?', null, function () {
        $.ajax({
            type: 'POST',
            url: '/api/trans_deliver',
            data: {id:order_id, doc:doc_id},
            dataType: 'json',
            success: function(data) {
                if(data.code == 200)
                {
                    Dcat.success('Success', 'Delivery Documents');
                    Dcat.reload();
                };
            }
        });
    });
}
function editor_deliver(order_id, doc_id){
    Dcat.confirm('Are you sure you want to deliver?', null, function () {
        $.ajax({
            type: 'POST',
            url: '/api/editor_deliver',
            data: {id:order_id, doc:doc_id},
            dataType: 'json',
            success: function(data) {
                if(data.code == 200)
                {
                    Dcat.success('Success', 'Delivery Documents');
                    Dcat.reload();
                };
            }
        });
    });
}
