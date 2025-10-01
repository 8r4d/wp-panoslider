<?php
/**
 * Plugin Name: Panoramic Slider for Gutenberg
 * Description: Gutenberg block for ultra-wide images with horizontal scroll, drag-to-scroll, arrows, mobile swipe, adjustable height, rounded corners, and optional scrollbar.
 * Version: 1.0.2
 * Author: Brad Salomons
 * Plugin URI:  https://8r4d.com/
 * Author URI:  https://8r4d.com/
 * License:     GPL-2.0+
 */

if (!defined('ABSPATH')) exit;

function panoramic_slider_block_init() {
    wp_register_script(
        'panoramic-slider-block',
        '',
        array('wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components'),
        false,
        true
    );

    $block_js = <<<JS
(function(blocks, element, blockEditor, components){
    var el = element.createElement;
    var MediaUpload = blockEditor.MediaUpload;
    var InspectorControls = blockEditor.InspectorControls;
    var Button = components.Button;
    var PanelBody = components.PanelBody;
    var RangeControl = components.RangeControl;
    var ToggleControl = components.ToggleControl;
    var Fragment = element.Fragment;

    function addSliderBehavior(container){
        if(!container) return;
        var isDown=false, startX, scrollLeft;
        var startTouchX, startScrollLeft;

        // Mouse drag
        container.addEventListener('mousedown', function(e){ isDown=true; startX=e.pageX-container.offsetLeft; scrollLeft=container.scrollLeft; });
        container.addEventListener('mouseleave', function(){ isDown=false; });
        container.addEventListener('mouseup', function(){ isDown=false; });
        container.addEventListener('mousemove', function(e){ if(!isDown) return; e.preventDefault(); container.scrollLeft=scrollLeft-(e.pageX-startX); });

        // Touch swipe
        container.addEventListener('touchstart', function(e){ startTouchX=e.touches[0].pageX; startScrollLeft=container.scrollLeft; });
        container.addEventListener('touchmove', function(e){ container.scrollLeft=startScrollLeft-(e.touches[0].pageX-startTouchX); });
    }

    blocks.registerBlockType('custom/panoramic-slider',{
        title:'Panoramic Slider',
        icon:'images-alt2',
        category:'media',
        attributes:{
            imageUrl:{type:'string',default:''},
            height:{type:'number',default:240},
            borderRadius:{type:'number',default:0},
            hideScrollbar:{type:'boolean',default:true}
        },
        edit:function(props){
            var attr=props.attributes,setAttr=props.setAttributes;
            var blockId='panoramic-'+Math.floor(Math.random()*1000000);

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, {title:'Panoramic Settings'},
                        el(RangeControl,{label:'Image Height (px)', value:attr.height, min:200, max:800, onChange:function(val){setAttr({height:val});}}),
                        el(RangeControl,{label:'Border Radius (px)', value:attr.borderRadius, min:0, max:100, onChange:function(val){setAttr({borderRadius:val});}}),
                        el(ToggleControl,{label:'Hide Scrollbar', checked:attr.hideScrollbar, onChange:function(val){setAttr({hideScrollbar:val});}})
                    )
                ),
                el('div',{className:'panoramic-slider-wrapper', style:{position:'relative'}},
                    el('div',{
                        id:blockId,
                        className:'panoramic-slider-editor',
                        style:{
                            height:attr.height+'px',
                            overflowX:attr.hideScrollbar?'hidden':'auto',
                            whiteSpace:'nowrap',
                            display:'flex',
                            alignItems:'center',
                            justifyContent:attr.imageUrl?'flex-start':'center'
                        }
                    },
                        attr.imageUrl
                            ? el('img',{src:attr.imageUrl, style:{height:attr.height+'px', borderRadius:attr.borderRadius+'px', userSelect:'none', pointerEvents:'none'}})
                            : el(MediaUpload,{
                                onSelect:function(media){setAttr({imageUrl:media.url});},
                                allowedTypes:['image'],
                                render:function(obj){ return el(Button,{onClick:obj.open,isPrimary:true, style:{width:'auto'}},'Select Image'); }
                            })
                    )
                ),
                el('script', {}, "addSliderBehavior(document.getElementById('"+blockId+"'));")
            );
        },
        save:function(props){
            var attr=props.attributes;
            return el('div',{className:'panoramic-slider-wrapper', style:{position:'relative'}},
                el('div',{className:'panoramic-slider', style:{height:attr.height+'px', overflowX:attr.hideScrollbar?'hidden':'auto', whiteSpace:'nowrap'}},
                    attr.imageUrl?el('img',{src:attr.imageUrl, style:{height:attr.height+'px', borderRadius:attr.borderRadius+'px'}}):null
                )
            );
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
JS;

    wp_add_inline_script('panoramic-slider-block', $block_js);

    function panoramic_slider_frontend_script(){
        ?>
        <script>
        window.addEventListener('load', function(){
            var wrappers=document.querySelectorAll('.panoramic-slider-wrapper');
            wrappers.forEach(function(wrapper){
                var container=wrapper.querySelector('.panoramic-slider');
                if(!container) return;

                // Drag-to-scroll
                var isDown=false, startX, scrollLeft;
                container.addEventListener('mousedown',function(e){isDown=true; startX=e.pageX-container.offsetLeft; scrollLeft=container.scrollLeft;});
                container.addEventListener('mouseleave',function(){isDown=false;});
                container.addEventListener('mouseup',function(){isDown=false;});
                container.addEventListener('mousemove',function(e){if(!isDown) return; e.preventDefault(); container.scrollLeft=scrollLeft-(e.pageX-startX);});

                // Touch swipe
                var startTouchX, startScrollLeft;
                container.addEventListener('touchstart',function(e){startTouchX=e.touches[0].pageX; startScrollLeft=container.scrollLeft;});
                container.addEventListener('touchmove',function(e){container.scrollLeft=startScrollLeft-(e.touches[0].pageX-startTouchX);});

                // Left arrow
                var leftBtn=document.createElement('button');
                leftBtn.classList.add('left-arrow');
                Object.assign(leftBtn.style,{position:'absolute', left:0, top:'50%', transform:'translateY(-50%)', zIndex:10, background:'rgba(0,0,0,0.3)', width:'36px', height:'36px', borderRadius:'5%', cursor:'pointer', opacity:0, transition:'opacity 0.3s'});
                leftBtn.onclick=function(){container.scrollLeft-=100;};
                wrapper.appendChild(leftBtn);

                // Right arrow
                var rightBtn=document.createElement('button');
                rightBtn.classList.add('right-arrow');
                Object.assign(rightBtn.style,{position:'absolute', right:0, top:'50%', transform:'translateY(-50%)', zIndex:10, background:'rgba(0,0,0,0.3)', width:'36px', height:'36px', borderRadius:'5%', cursor:'pointer', opacity:0, transition:'opacity 0.3s'});
                rightBtn.onclick=function(){container.scrollLeft+=100;};
                wrapper.appendChild(rightBtn);

                // Fade in/out
                wrapper.addEventListener('mouseenter',function(){leftBtn.style.opacity=1; rightBtn.style.opacity=1;});
                wrapper.addEventListener('mouseleave',function(){leftBtn.style.opacity=0; rightBtn.style.opacity=0;});
            });
        });
        </script>
        <?php
    }
    add_action('wp_footer','panoramic_slider_frontend_script');

    // CSS
    $block_css = <<<CSS
.panoramic-slider, .panoramic-slider-editor {
    overflow-y:hidden;
    white-space:nowrap;
    position:relative;
}
.panoramic-slider img, .panoramic-slider-editor img {
    display:block;
    height:100%;
    width:auto;
    user-select:none;
    pointer-events:none;
}

/* Arrows as CSS triangles */
.panoramic-slider-wrapper .left-arrow::before,
.panoramic-slider-wrapper .right-arrow::before {
    content:'';
    display:block;
    width:0; height:0;
    margin:auto;
}
.panoramic-slider-wrapper .left-arrow::before {
    border-top:8px solid transparent;
    border-bottom:8px solid transparent;
    border-right:12px solid white;
}
.panoramic-slider-wrapper .right-arrow::before {
    border-top:8px solid transparent;
    border-bottom:8px solid transparent;
    border-left:12px solid white;
}

/* Button hover/fade */
.panoramic-slider-wrapper button:hover {
    background: rgba(0,0,0,0.5);
}
.panoramic-slider-wrapper button {
    border:none;
    font-size:20px;
    user-select:none;
}
CSS;

    wp_register_style('panoramic-slider-style', false);
    wp_add_inline_style('panoramic-slider-style', $block_css);

    register_block_type('custom/panoramic-slider', array(
        'editor_script' => 'panoramic-slider-block',
        'style'         => 'panoramic-slider-style',
    ));
}
add_action('init','panoramic_slider_block_init');
