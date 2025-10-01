(function (blocks, element, blockEditor, components) {
    var el = element.createElement;
    var MediaUpload = blockEditor.MediaUpload;
    var InspectorControls = blockEditor.InspectorControls;
    var Button = components.Button;
    var PanelBody = components.PanelBody;
    var RangeControl = components.RangeControl;
    var ToggleControl = components.ToggleControl;
    var Fragment = element.Fragment;

    blocks.registerBlockType('custom/panoramic-slider', {
        title: 'Panoramic Slider',
        icon: 'images-alt2',
        category: 'media',
        attributes: {
            imageUrl: { type: 'string', default: '' },
            height: { type: 'number', default: 240 },
            borderRadius: { type: 'number', default: 0 },
            hideScrollbar: { type: 'boolean', default: true }
        },
        edit: function (props) {
            var attr = props.attributes, setAttr = props.setAttributes;

            return el(Fragment, {},
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Panoramic Settings' },
                        el(RangeControl, {
                            label: 'Image Height (px)',
                            value: attr.height,
                            min: 200, max: 800,
                            onChange: function (val) { setAttr({ height: val }); }
                        }),
                        el(RangeControl, {
                            label: 'Border Radius (px)',
                            value: attr.borderRadius,
                            min: 0, max: 100,
                            onChange: function (val) { setAttr({ borderRadius: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Hide Scrollbar',
                            checked: attr.hideScrollbar,
                            onChange: function (val) { setAttr({ hideScrollbar: val }); }
                        })
                    )
                ),
                el('div', { className: 'panoramic-slider-wrapper', style: { position: 'relative' } },
                    el('div', {
                        className: 'panoramic-slider-editor',
                        style: {
                            height: attr.height + 'px',
                            overflowX: attr.hideScrollbar ? 'hidden' : 'auto',
                            whiteSpace: 'nowrap',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: attr.imageUrl ? 'flex-start' : 'center'
                        }
                    },
                        attr.imageUrl
                            ? el('img', { src: attr.imageUrl, style: { height: attr.height + 'px', borderRadius: attr.borderRadius + 'px' } })
                            : el(MediaUpload, {
                                onSelect: function (media) { setAttr({ imageUrl: media.url }); },
                                allowedTypes: ['image'],
                                render: function (obj) { return el(Button, { onClick: obj.open, isPrimary: true, style: { width: 'auto' } }, 'Select Image'); }
                            })
                    )
                )
            );
        },
        save: function (props) {
            var attr = props.attributes;
            return el('div', { className: 'panoramic-slider-wrapper', style: { position: 'relative' } },
                el('div', {
                    className: 'panoramic-slider',
                    style: {
                        height: attr.height + 'px',
                        overflowX: attr.hideScrollbar ? 'hidden' : 'auto',
                        whiteSpace: 'nowrap'
                    }
                },
                    attr.imageUrl ? el('img', { src: attr.imageUrl, style: { height: attr.height + 'px', borderRadius: attr.borderRadius + 'px' } }) : null
                )
            );
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components);
