;(function($){
$.jgrid.extend({
    tableResize : function() {
        this.each(function(){
            var tableWidth = 0;
            var gID = this.p.id;
            for(i=0;i<this.p.colNames.length;i++){
                if(!this.p.colModel[i].hidedlg) {
                    if(!this.p.colModel[i].hidden){
                        tableWidth += parseInt(this.p.colModel[i].widthOrg);
                    }
                }
            }
            $("#"+gID).setGridHeight($(window).height() - 200);
            if($(window).width()>tableWidth){
                $("#"+gID).setGridWidth($(window).width());
            }else{
                $("#"+gID).setGridWidth(tableWidth);
            }
        });
    }
});
})(jQuery);


;(function($){

$.jgrid.extend({
    setColumns : function() {
        this.each(function(){
            var $t = this;
            var gID = $t.p.id;
            var dtbl = "ColTbl_"+gID;
            var tableWidth = 0;

            if ($("#"+dtbl).html() == null ) {
                $str = '<div class="modal fade" id="'+ dtbl +'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
                    $str += '<div class="modal-dialog">',
                        $str += '<div class="modal-content">';
                            $str += '<div class="modal-header">';
                                $str += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
                                $str += '<h4 class="modal-title" id="myModalLabel">列展示隐藏管理</h4>';
                            $str += '</div>';
                            $str += '<div class="modal-body">';
                                $str += '<div class="container" style="width: 100%;" >';
                                    $str += '<div class="row">';
                                    for(i=0;i<this.p.colNames.length;i++){
                                        if(!this.p.colModel[i].hidedlg) {
                                            $str += '<div class="col-xs-3" style="margin-bottom:3px;">';
                                                $str +='<button type="button"  grid="'+gID+'" modelname="'+this.p.colModel[i].name+'" class="columns btn '+(this.p.colModel[i].hidden?'btn-default':'btn-success')+'">'+ this.p.colNames[i] +'</button>';
                                            $str += '</div>';
                                        }
                                    }
                                    $str += '</div>';
                                $str += '</div>';
                            $str += '</div>';
                        $str += '</div>';
                    $str += '</div>';
                $str += '</div>';
                $('#'+gID).parent().append($str);
            }
            $('#'+dtbl).modal('toggle');
        });
        $(document).on('click','.columns',function(){
            var grid = $(this).attr('grid');
            var modelname = $(this).attr('modelname');
            if($(this).hasClass('btn-success')){
                $('#'+grid).hideCol(modelname);
                $(this).removeClass('btn-success').addClass('btn-default');
                $("#"+grid).tableResize();
            }else{
                $('#'+grid).showCol(modelname);
                $(this).removeClass('btn-default').addClass('btn-success');
                $("#"+grid).tableResize();
            }
        });
    }
});
})(jQuery);




