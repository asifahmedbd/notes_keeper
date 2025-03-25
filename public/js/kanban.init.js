!function(e){
    "use strict";
    var o=function(){
        this.$body=e("body")
    };
    e("#parent_menu, #sub_menu").sortable({
        connectWith:".taskList",
        placeholder:"task-placeholder",
        forcePlaceholderSize:!0,
        update:function(o,t){

            console.log();

            e("#todo").sortable("toArray"), e("#parent_menu").sortable("toArray"), e("#sub_menu").sortable("toArray")
        }
    }).disableSelection(),o.prototype.init=function(){},
        e.KanbanBoard=new o,
        e.KanbanBoard.Constructor=o
}(window.jQuery),function(o){
    "use strict";
    window.jQuery.KanbanBoard.init()
}();