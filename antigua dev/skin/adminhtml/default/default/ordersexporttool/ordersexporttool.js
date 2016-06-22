
function setSelectionRange(input, selectionStart, selectionEnd) {
    if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
    }
    else if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
    }
}

function replaceSelection (input, replaceString) {
    if (input.setSelectionRange) {
        var selectionStart = input.selectionStart;
        var selectionEnd = input.selectionEnd;
        input.value = input.value.substring(0, selectionStart)+ replaceString + input.value.substring(selectionEnd);
    
        if (selectionStart != selectionEnd){ 
            setSelectionRange(input, selectionStart, selectionStart + 	replaceString.length);
        }else{
            setSelectionRange(input, selectionStart + replaceString.length, selectionStart + replaceString.length);
        }

    }else if (document.selection) {
        var range = document.selection.createRange();

        if (range.parentElement() == input) {
            var isCollapsed = range.text == '';
            range.text = replaceString;

            if (!isCollapsed)  {
                range.moveStart('character', -replaceString.length);
                range.select();
            }
        }
    }
}


// We are going to catch the TAB key so that we can use it, Hooray!
function catchTab(item,e){
    if(navigator.userAgent.match("Gecko")){
        c=e.which;
    }else{
        c=e.keyCode;
    }
    if(c==9){
        replaceSelection(item,String.fromCharCode(9));
        setTimeout("document.getElementById('"+item.id+"').focus();",0);	
        return false;
    }
}



var ordersexporttool={
    _delete:function(order,profil,url){
        if(confirm('Are you sure ?')){
            data={order:order,profil:profil};
            new Ajax.Request(url,{
                parameters:data,
                method:'post',
                onSuccess: function(){
                    $('orderexported-'+order+'-'+profil).select('a')[0].remove();
                    $('orderexported-'+order+'-'+profil).setStyle({textDecoration:'line-through'})
                }
                
            })
        }
    } ,
    setValues:function (selector){
        selection=new Array;
        selector.select('INPUT[type=checkbox]').each(function(i){
            if(selector.id=='attributes-selector'){
		
                attribute={}
                attribute.line=i.readAttribute('identifier');
                attribute.checked=i.checked;
                attribute.code=i.next().value;
                attribute.condition=i.next().next().value;
                attribute.value=i.next().next().next().next().value;
                selection.push(attribute);
            }
           
            else if(i.checked==true)selection.push(i.readAttribute('identifier'));
		
        })
        switch(selector.id){
           
            case 'attributes-selector' :
                $('file_attributes').value=Object.toJSON(selection);
                break;
            case 'states-selector' :
                $('file_states').value=selection.join(',');
                break;
            case 'customer-groups-selector' :
                $('file_customer_groups').value=selection.join(',');
                break;
        }
	
    },
    
   
    /*
     * Passer en mode txt / csv  
     * 
     */
    clearFields : function(){
        $('file_header').value='';
        $('file_body').value='';
        $('file_footer').value='';
			
    },
    /*
     * Passer en mode txt / csv  
     * 
     */
    textMode : function(){
			
        $$('.txt-type').each(function(f){
            f.ancestors()[1].show()
				
        })
        $$('.txt-type:not(.not-required)').each(function(f){
            f.addClassName('required-entry')
			
        })
        $$('.xml-type').each(function(f){
            f.ancestors()[1].hide()
            f.removeClassName('required-entry')
        })
			
        $('file_header').ancestors()[1].hide();
        $('file_body').ancestors()[1].hide();
			
        
		
		
        myContent=Builder.node('span',{
            className:'fields-mapping'
        },[
            Builder.node('div',{
                className:'mapping'
            },['Columns name',
                Builder.node('span',{
                    style:'margin-left:96px'
                },'Pattern')]),               
            Builder.node('ul',{
                className:'txt-field-box',
                id:'txt-fields-box'
            })
        ])
        $('file_extra_header').insert({
            after:myContent
        });
			
        input=Builder.node('BUTTON',{
            className:'add-field ',
            type:'button',
            onclick:'ordersexporttool.addTextField(\'\',\'\');ordersexporttool.checkSyntax()'
        },['Add field'])
        $('txt-fields-box').insert({
            after:input
        });
	       
        if($('file_header').value!="" && $('file_body').value!="")ordersexporttool.jsonToTextFields();
			
        $('ordersexporttool_form').addClassName('text')
    },
		
    /*
     * Ajouter une ligne de champs de textes
     * 
     */
    addTextField : function(head, prod){
        input=Builder.node('LI',[ 	
            Builder.node('INPUT',{
                className:'txt-field  header-txt-field input-text refresh', 
                value:head,
                onkeyup:'ordersexporttool.checkSyntax()'
            }),
            Builder.node('INPUT',{
                className:'txt-field  order-txt-field input-text refresh',
                value:prod,
                onkeyup:'ordersexporttool.checkSyntax()'
            }),
            Builder.node('BUTTON',{
                className:'remove-field refresh',
                type:'button', 
                onclick:'ordersexporttool.removeTextField(this);ordersexporttool.checkSyntax()'
            },['\u2716']),
            Builder.node('BUTTON',{
                className:'move-field-up refresh',
                type:'button', 
                onclick:'ordersexporttool.moveField(this,"up");ordersexporttool.checkSyntax()'
            }),
            Builder.node('BUTTON',{
                className:'move-field-down refresh',
                type:'button', 
                onclick:'ordersexporttool.moveField(this,"down");ordersexporttool.checkSyntax()'
            }),
        ])
        input.select('BUTTON')[1].update('&uarr;');
        input.select('BUTTON')[2].update('&darr;');
        $('txt-fields-box').insert({
            bottom:input
        });
    },
		
    /*
     * Supprimer une ligne de champs de textes
     * 
     */
    removeTextField : function(elt){
        elt.ancestors()[0].remove();
    },
		
    /*
     * D�placer une ligne de champs de textes
     * 
     */
    moveField : function(elt,direction){
			
        li=elt.ancestors()[0];
			
        index=$('txt-fields-box').select('LI').indexOf(li);
        if (index>0)  prev=index-1; 
        else prev=1;
			
        if (index<$('txt-fields-box').select('LI').length-1)  next=index+1; 
        else next=$('txt-fields-box').select('LI').length-2;
			
        prevli=$('txt-fields-box').select('LI')[prev];
        nextli=$('txt-fields-box').select('LI')[next];
          
        li.remove();
			
        switch(direction){
            case 'up' :
                prevli.insert({
                    before:li
                })
                break;
            default :
                nextli.insert({
                    after:li
                })
                break;
			
        }
    },
    /*
     * Parser le json en lignes de champs de textes
     * 
     */
    jsonToTextFields : function(){
	
        data=new Object;	
        header=$('file_header').value.evalJSON().header;
        order=$('file_body').value.evalJSON().order;
        data.header=header;
        data.order=order;
			
        i=0;
        data.order.each(function(){
				
            ordersexporttool.addTextField(data.header[i],data.order[i]);
            i++;
        })
		
			
    },
    /*
     * Parser les lignes de champs de textes en JSON
     * 
     */
    textFieldsToJson : function(){
		
        data=new Object;	
        data.header=new Array;
        c=0;
        $('txt-fields-box').select('INPUT.header-txt-field').each(function(i){
            data.header[c]=i.value;
            c++;
        })
        data.order=new Array;
        c=0;
        $('txt-fields-box').select('INPUT.order-txt-field').each(function(i){
            data.order[c]=i.value;
            c++;
        })
        $('file_header').value='{"header":'+Object.toJSON(data.header)+"}";
        $('file_body').value='{"order":'+Object.toJSON(data.order)+"}";
			
    },
   
   	
    /*
     * Passer en mode xml
     * 
     */
    xmlMode : function(){
			
        $$('.fields-mapping').each(function(t){
            t.remove()
        });
			
			
        $$('.txt-type').each(function(f){
            f.ancestors()[1].hide();
            f.removeClassName('required-entry')
        })
        $$('.xml-type').each(function(f){
            f.ancestors()[1].show()
            f.addClassName('required-entry')
        })
			
        $('file_header').ancestors()[1].show();
        $('file_body').ancestors()[1].show();
			
        $('ordersexporttool_form').removeClassName('text')
    },
	
    /*
     * ouvrir/fermer la preview 
     * 
     */
    switchStatus: function(){
        if( $('dfm-console').hasClassName('arr_down')){
            $('dfm-console').removeClassName('arr_down')
            $('dfm-console').addClassName('arr_up')
            $$('#dfm-console #page')[0].setStyle({"visibility":"visible"});
        }
        else{
            $('dfm-console').removeClassName('arr_up')
            $('dfm-console').addClassName('arr_down')
            $$('#dfm-console #page')[0].setStyle({"visibility":"hidden"});
        }
    },
    
    /*
     * étendre/réduire la preview 
     * 
     */
    storage:{
        top: null,
        left:null,
        width:null,
        height:null
    },
    switchSize: function(){
        $('dfm-console').addClassName('resize');
        if( $('dfm-console').hasClassName('reduce')){
            $('dfm-console').removeClassName('reduce')
            $('dfm-console').addClassName('full')
            ordersexporttool.storage.top= $('dfm-console').getStyle('top');
            ordersexporttool.storage.left=$('dfm-console').getStyle('left');
            $('dfm-console').setStyle({
                top:'10px',
                left:'10px'
            })
            ordersexporttool.storage.width=$('page').getStyle('width');
            ordersexporttool.storage.height= $('page').getStyle('height');
            $('page').setStyle({
                width:(document.viewport.getDimensions().width-40)+'px',
                height:(document.viewport.getDimensions().height-150)+'px'
            })
          
        }
        else{
            $('dfm-console').removeClassName('full')
            $('dfm-console').addClassName('reduce')
            $('dfm-console').setStyle({
                top:ordersexporttool.storage.top,
                left:ordersexporttool.storage.left
            })
            $('page').setStyle({
                width:ordersexporttool.storage.width,
                height:ordersexporttool.storage.height
            })
        }
        setTimeout(function(){
            $('dfm-console').removeClassName('resize')
        },300);
    },
   

    /*
     * Mise � jour des donn�es 
     * 
     */
    checkSyntax:function(){
        if(!ordersexporttool.isXmlMode())
            ordersexporttool.textFieldsToJson();
        
        // nom du fichier
        $('dfm-console').select('.filename')[0].update($('file_name').value)
        $('dfm-console').select('.filetype')[0].update($('file_type').options[$('file_type').selectedIndex].innerHTML)
         
        if(!ordersexporttool.isXmlMode()){
            
            H=$('file_header').value.evalJSON().header;
            P=$('file_body').value.evalJSON().order;
            
            
            textContent="<file format=\""+$('file_type').options[$('file_type').selectedIndex].innerHTML+"\" delimiter=\""+$('file_separator').options[$('file_separator').selectedIndex].innerHTML+"\" ";
           
            textContent+="header='"+$('file_include_header').options[$('file_include_header').selectedIndex].innerHTML+"' ";
            if($('file_protector').value!="\"")textContent+="enclosure=\""+$('file_protector').options[$('file_protector').selectedIndex].innerHTML+"\" ";
            else textContent+="enclosure='"+$('file_protector').options[$('file_protector').selectedIndex].innerHTML+"' ";
            textContent+=">\n";
            for(h=0;h<H.length;h++){
                textContent+="  <column position='"+(h+1)+"' name='"+H[h]+"'>\n    "+P[h]+"\n  </column>\n";
            }
            textContent+="</file>";
            
            
            ordersexporttool.CodeMirror = CodeMirror(function(elt) {
                $$('.CodeMirror')[0].parentNode.replaceChild(elt, $$('.CodeMirror')[0])
            }, {
                value:textContent,
                mode: 'xml',
                readOnly: true

            })
        }
        else{
            ordersexporttool.CodeMirror = CodeMirror(function(elt) {
                $$('.CodeMirror')[0].parentNode.replaceChild(elt, $$('.CodeMirror')[0])
            }, {
                value:$('file_header').value+"\n"+$('file_body').value+"\n"+$('file_footer').value,
                mode:  'xml',
                readOnly: true

            })
       
        }
        
        ordersexporttool.enligthSyntax();
       
    },
    
    enligthSyntax: function(){
       
        clearTimeout(ordersexporttool.timer) 
        ordersexporttool.timer=setTimeout(function(){
            $$('.cm-dfm').each(function(cm){
                
                cm.update(ordersexporttool.enlighter(cm.innerHTML))
            
            })
        },150)
    },
    
    checkLibrary:function(){
        $('page').addClassName('loader')
        $('page').childElements()[0].setStyle({
            display:'none'
        });
      
        url=$('library_url').getValue();
        // mise � jour des textarea si mode text
            
        new Ajax.Request(url,{
               
                
            onSuccess: function(response){
                $('page').childElements()[0].setStyle({
                    display:'block'
                });
                $('page').removeClassName('loader')
        
                $$('.CodeMirror')[0].update(response.responseText)
                
                
            }
                
        })
    },
    
    updatePreview:function(){
         
        // nom du fichier
        $('dfm-console').select('.filename')[0].update($('file_name').value)
        $('dfm-console').select('.filetype')[0].update($('file_type').options[$('file_type').selectedIndex].innerHTML)
        
        $('page').addClassName('loader')
        $('page').childElements()[0].setStyle({
            display:'none'
        });
      
        url=$('sample_url').getValue();
        // mise � jour des textarea si mode text
        if(!ordersexporttool.isXmlMode()){
            ordersexporttool.textFieldsToJson();
            data=Form.serialize($$('FORM')[0],true);
            
            new Ajax.Request(url,{
                parameters:data,
                method:'post',
           
                onSuccess: function(response){
                    $('page').childElements()[0].setStyle({
                        display:'block'
                    });
                    $('page').removeClassName('loader')
        
                    $$('.CodeMirror')[0].update(response.responseText)
              
                
                }
                
            })
        }else{
            data=Form.serialize($$('FORM')[0],true);
       
            new Ajax.Request(url,{
                parameters:data,
                method:'post',
           
                onSuccess: function(response){
                    $('page').childElements()[0].setStyle({
                        display:'block'
                    });
                    $('page').removeClassName('loader')
                
                    if(response.responseText.indexOf("<!DOCTYPE html")==-1){
                        ordersexporttool.CodeMirror = CodeMirror(function(elt) {
                            $$('.CodeMirror')[0].parentNode.replaceChild(elt, $$('.CodeMirror')[0])
                        }, {
                            value:response.responseText,
                            mode:  'xml',
                            readOnly: true

                        })
                    }
                    else  $$('.CodeMirror')[0].update(response.responseText);
                   
                   
                  
                }
                
            })
        }
            
      		
    },
    enlighter: function(text){
		
        // tags
        text=text.replace(/<([^?^!]{1}|[\/]{1})(.[^>]*)>/g,"<span class='blue'>"+"<$1$2>".escapeHTML()+"</span>")
			
        // comments
        text=text.replace(/<!--/g,"¤");
        text=text.replace(/-->/g,"¤");
        text=text.replace(/¤([^¤]*)¤/g,"<span class='green'>"+"<!--$1-->".escapeHTML()+"</span>");
			
        // php code
        text=text.replace(/<\?/g,"¤");
        text=text.replace(/\?>/g,"¤");
        text=text.replace(/¤([^¤]*)¤/g,"<span class='orange'>"+"<?$1?>".escapeHTML()+"</span>");
        // attributs + 6 options 
        text=text.replace(/\{([^\s}[:]*)(\sorder|\sbilling|\sshipping|\sproduct|\sinvoice|\sshipment|\screditmemo|\spayment)?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?((,)(\[.[^\]]*\]))?\}/g,"<span class='pink'>{$1<span class='grey'>$2</span>$4<span class='green'>$5</span>$7<span class='green'>$8</span>$10<span class='green'>$11</span>$13<span class='green'>$14</span>$16<span class='green'>$17</span>$19<span class='green'>$20</span>}</span>");
        // attribut Loop start
        text=text.replace(/\{(product|invoice|shipment|creditmemo|payment)(::start)\}/g,"<span class='orangered'>{$1<span class='green'>$2</span>}</span>");
        // attribut Loop end
        text=text.replace(/\{(product|invoice|shipment|creditmemo|payment)(::end)\}/g,"<span class='orangered'>{$1<span class='red'>$2</span>}</span>");
        
			
        return text;
    },
	
    currentMode:null ,
    /*
     * Savoir si on est en mode xml ou non
     * 
     */
		
    isXmlMode: function (){
        if($('file_type').value==1) return true;
        else return false
    },
    /*
     * Renvoie l'id du mode
     * 
     */

    getIdMode: function (){
			
        return $('file_type').value;
    },
    /*
     * R�gle l'id du mode
     * 
     */

    setIdMode: function (id){
			
        $('file_type').value=id;
    },
    /*
     * Changer de mode
     * 
     */
    changeMode : function (){
			
        if(ordersexporttool.currentMode==null ){
            ordersexporttool.currentMode=ordersexporttool.getIdMode();
            if(ordersexporttool.isXmlMode()) ordersexporttool.xmlMode();
            else ordersexporttool.textMode();
			
        }
        else{
            if((ordersexporttool.currentMode>1 && ordersexporttool.getIdMode()==1)|(ordersexporttool.currentMode==1 && ordersexporttool.getIdMode()>1) ){
                if(confirm("Changing file type from/to xml will clear all your setting.\ Do you want to continue ?")){
                    ordersexporttool.clearFields();
                    if(ordersexporttool.isXmlMode()) ordersexporttool.xmlMode();
                    else ordersexporttool.textMode();
                    ordersexporttool.setIdMode(ordersexporttool.getIdMode());
					
                }
                else  ordersexporttool.setIdMode(ordersexporttool.currentMode);
                ordersexporttool.currentMode=ordersexporttool.getIdMode();
            }
        }
        ordersexporttool.checkSyntax();
			
    }
		
}
document.observe('click',function(e){
    
    
   	
    if(e.findElement('input[type=checkbox]')){ 
        i=e.findElement('input[type=checkbox]');
		
        i.ancestors().each(function(a){
            if(a.hasClassName('fieldset')) 	selector=$(a.id);
        })
      
        if(selector.id=='attributes-selector'){
            if(i.checked==true)	i.ancestors()[1].select('div')[0].select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                h.disabled=false
            })
            else i.ancestors()[1].select('div')[0].select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                h.disabled=true
            })
        }
			
      
		
        ordersexporttool.setValues(selector);
		
		
        selector.select('.selected').each(function(s){
            s.removeClassName('selected')
        })
        selector.select('.node').each(function(li){
            if(li.select('INPUT')[0].checked==true){
                li.addClassName('selected');
				
            }
        })
    }
})


document.observe('dom:loaded', function(){
    
    if(!$('file_scheduled_task').value.isJSON())$('file_scheduled_task').value='{"days":[],"hours":[]}';
    cron=$('file_scheduled_task').value.evalJSON();
        
       
    cron.days.each(function(d){
        if($('d-'+d)){
            $('d-'+d).checked=true;
            $('d-'+d).ancestors()[0].addClassName('checked');
        }
            
    })
    cron.hours.each(function(h){
        if( $('h-'+h.replace(':',''))){
            $('h-'+h.replace(':','')).checked=true;
            $('h-'+h.replace(':','')).ancestors()[0].addClassName('checked');
        }
    })
        
    $$('.cron-box').each(function(e){
        e.observe('click',function(){
                
            if(e.checked)e.ancestors()[0].addClassName('checked');
            else e.ancestors()[0].removeClassName('checked');
               
            d=new Array
            $$('.cron-d-box INPUT').each(function(e){
                if(e.checked) d.push(e.value)
            })
            h=new Array;
            $$('.cron-h-box INPUT').each(function(e){
                if(e.checked) h.push(e.value)
            })
                
            $('file_scheduled_task').value=Object.toJSON({
                days:d,
                hours:h
            })
               
        }) 
    })
	
    
    if($('file_states').value!=''){

        $('file_states').value.split(',').each(function(e){
            $('state_'+e).checked=true;
            $('state_'+e).ancestors()[1].addClassName('selected');
        });
    }
    if($('file_customer_groups').value!=''){
       
        $('file_customer_groups').value.split(',').each(function(e){
            $('customer_group_'+e).checked=true;
            $('customer_group_'+e).ancestors()[1].addClassName('selected');
        });
    }
    
 
    if($('file_attributes').value=='')$('file_attributes').value="[]";
    attributes=$('file_attributes').value.evalJSON();
    
    if(attributes.length>0){
        attributes.each(function(attribute){
 
            if(attribute.checked){
                $('attribute_'+attribute.line).checked=true;
                $('node_'+attribute.line).addClassName('selected');
                $('node_'+attribute.line).select('INPUT:not(INPUT[type=checkbox]),SELECT').each(function(h){
                    h.disabled=false
                })
            }
            $('name_attribute_'+attribute.line).value=attribute.code;
            $('condition_attribute_'+attribute.line).value=attribute.condition;
            $('value_attribute_'+attribute.line).value=attribute.value;
        });
    }
     
    $('attributes-selector').select('SELECT').each(function(n){
         
        if(n.hasClassName('name-attribute')){
            prefilledValues=n.next().next();
            eval("options="+n.value);
            
            html=null;
            custom=true;
            if(options.length>0){
                options.each(function(o){
                    if (prefilledValues.next().value.split(',').indexOf(o.value+'')!=-1){
                        selected='selected'
                        custom=false;
                    }
                    else{
                        selected=false;
                    }
                
                    html+="<option value='"+o.value+"' "+selected+">"+o.label+"</option>";
                })
                if(custom)selected="selected";
                else selected='';
                html+="<option value='_novalue_' style='color:#555' "+selected+">custom value...</option>";
            
          
                if(!custom){
                          
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'none'
                        
                    }) 
                    /* r=[];
                    prefilledValues.select('OPTION').each(function(e){
                        if(e.selected) r.push(e.value)
                    })
                    r=r.join(',')
                    prefilledValues.next().value=r;
                     */
                }
                else {
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display': 'block',
                        'margin': '0 0 0 422px'
                        
                    }) 
                }
                prefilledValues.update(html)
                
                
                
            }
            
            
            n.observe('change',function(){
             
                prefilledValues=n.next().next();
                eval("options="+n.value);
                html="";
                options.each(function(o){
                    (o.value==prefilledValues.next().value)? selected='selected':selected=null;
                
                    html+="<option value='"+o.value+"' "+selected+">"+o.label+"</option>";
                })
                
                html+="<option value='_novalue_' style='color:#555'>custom value...</option>";
                if(options.length>0){
                   
                    prefilledValues.setStyle({
                        'display':'inline'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'none'
                       
                    }) 
                   
                    prefilledValues.update(html)
                    
                   
                }
                else{
                    prefilledValues.setStyle({
                        'display':'none'
                        
                    });
                    prefilledValues.next().setStyle({
                        'display':'inline',
                        'margin': '0 0 0 0'
                       
                    }) 
                    prefilledValues.next().value=null;
                    
                }
                prefilledValues.next().value=null
                ordersexporttool.setValues($("attributes-selector"))
            })
        }
    })
    $$('.pre-value-attribute').each(function(prefilledValues){
        prefilledValues.observe('change',function(){
                       
            if(prefilledValues.value!='_novalue_'){
                          
                prefilledValues.setStyle({
                    'display':'inline'
                    
                });
                prefilledValues.next().setStyle({
                    'display':'none'
                    
                }) 
                r=[];
                prefilledValues.select('OPTION').each(function(e){
                    if(e.selected) r.push(e.value)
                })
                r=r.join(',')
                     
                prefilledValues.next().value=r;
                ordersexporttool.setValues($("attributes-selector"))
               
            }
            else {
                prefilledValues.setStyle({
                    'display':'inline'
                   
                });
                prefilledValues.next().setStyle({
                    'display': 'block',
                    'margin': '0 0 0 422px'
                }) 
                
            }
                       
        })
    })
    
    $$('.refresh').each(function(f){
        f.observe('keyup', function(){
       
            ordersexporttool.checkSyntax()
        })
    })
    $$('.refresh').each(function(f){
        f.observe('change', function(){
            ordersexporttool.checkSyntax()
        })
    })
    $$('TEXTAREA').each(function(f){
        f.observe('keydown', function(event){
            catchTab(f, event)
        })
    })
    
    window.onresize = function() {
        ordersexporttool.checkSyntax()
    }
    
   
    
    $('loading-mask').remove();
	
    page=Builder.node('div',{
        id:'dfm-console',
        'class':'arr_up reduce'
    },[
        Builder.node('DIV',{
            id:"handler"
        },[
                                  
            Builder.node('span',{
                className:'filename'
            },'exemple'),
            Builder.node('BUTTON',{
                'class':'scalable',
                id:'closer',
                'onclick':'javascript:ordersexporttool.switchSize()'
            }, [
                Builder.node('SPAN',{
                    'class':'full'
                },'\u271a'),
                Builder.node('SPAN',{
                    'class':'reduce'
                },'\u2716')
            ]),
            Builder.node('BUTTON',{
                'class':'scalable',
                id:'closer',
                'onclick':'javascript:ordersexporttool.switchStatus()'
            },[
                Builder.node('SPAN',{
                    'class':'arr_up'
                },'\u25b2'),
                Builder.node('SPAN',{
                    'class':'arr_down'
                },'\u25bc')
            ]
        ),
            Builder.node('span','.'),
            Builder.node('span',{
                'class':'filetype'
            },'xml')]
    ),
        Builder.node('div',{
            id:'page'
        },
        Builder.node('textarea',{
            'class':'CodeMirror'
        })
    ),
        Builder.node('BUTTON',{
            'class':'scalable save',
            id:'refresher',
            'onclick':'javascript:ordersexporttool.updatePreview()'
        },
        Builder.node('SPAN','Check data')
    ),
        Builder.node('BUTTON',{
            'class':'scalable save',
            id:'library',
            'onclick':'javascript:ordersexporttool.checkLibrary()'
        },
        Builder.node('SPAN','Load library')
    ),

        Builder.node('BUTTON',{
            'class':'scalable save',
            id:'syntaxer',
            'onclick':'javascript:ordersexporttool.checkSyntax()'
        },
        Builder.node('SPAN','Check Syntax')
    )
    ])
    
    
    
    $$('BODY')[0].insert({
        top:page
    });
    new Draggable('dfm-console',{
        handle:'handler'
    });
    
	
    $('file_type').observe('change',function(){
        ordersexporttool.changeMode();
    })
	
    $$('.CodeMirror')[0]=$('CodeMirror');
    
    ordersexporttool.changeMode();
    ordersexporttool.checkSyntax();
 	
})