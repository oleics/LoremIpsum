
/*  */
var LoremIpsum = function(config) {
    config = config || {};
    
    /* Grid configuration options */
    Ext.applyIf(config, {
    });
    
    /* Class parent constructor */
    LoremIpsum.superclass.constructor.call(this,config);
};
Ext.extend(LoremIpsum,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('loremipsum', LoremIpsum);
LoremIpsum = new LoremIpsum();
