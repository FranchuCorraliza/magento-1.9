document.observe('dom:loaded', function(){
  
    var editor = CodeMirror.fromTextArea(document.getElementById("attribute_script"), {
        mode:  'application/x-httpd-php',
        onChange:function(){
            editor.save() 
          
            
        }
    });
   
    
    $$('.CodeMirror-scroll')[0].setStyle({
        'background':' none repeat scroll 0 0 white',
        'border':' 1px solid #BBBBBB',
        'width':'100%'
    })
    
    document.observe('unload', function(){
        alert('ok'); 
        editor.toTextArea()
    })
   
       
    
    
})