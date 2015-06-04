CKEDITOR.plugins.add( 'bootstrap',
{   
   requires : ['richcombo'],
   init : function( editor )
   {
      var config = editor.config,
         lang = editor.lang.format;

      var tags = [];
      tags[0]=["[bootstrap_alert style=%27info%27] My Content [/bootstrap_alert]", "Alert", "Alert"];
      tags[1]=["[bootstrap_align position=%27left%27] My Content [/bootstrap_align]", "Align", "Align"];
      tags[2]=["[bootstrap_badge style=%27%27] My Content [/bootstrap_badge]", "Badge", "Badge"];
      tags[3]=["[bootstrap_carousel slug=%27Gallery Slug%27]", "Carousel", "Carousel"];
      tags[4]=["[bootstrap_collapse title=%27My Title%27] My Content  [/bootstrap_collapse]", "Collapse", "Collapse"];
      tags[5]=["[bootstrap_emphasis style=%27%27] My Content  [/bootstrap_emphasis]", "Emphasis", "Emphasis"];
      tags[6]=["[bootstrap_hero] My Content  [/bootstrap_hero]", "Hero", "Hero"];
      tags[7]=["[bootstrap_label style=%27%27] My Content [/bootstrap_label]", "Label", "Label"];
      tags[8]=["[bootstrap_lead] My Content  [/bootstrap_lead]", "Lead", "Lead"];
      tags[9]=["[bootstrap_row] [bootstrap_span size=%27%27] My Content  [/bootstrap_span] [/bootstrap_row]", "Row - Span", "Row - Span"];
      tags[10]=["[bootstrap_tabheaderwrap][bootstrap_tabheader title=%27Tab One%27 active=%27true%27][bootstrap_tabheader title=%27Tab Two%27][/bootstrap_tabheaderwrap][bootstrap_tabcontentwrap][bootstrap_tabcontent title=%27Tab One%27 active=%27true%27]Tab one contents goes here[/bootstrap_tabcontent][bootstrap_tabcontent title=%27Tab Two%27]Tab two contents goes here[/bootstrap_tabcontent][/bootstrap_tabcontentwrap]", "Tabs", "Tabs"];
      tags[11]=["[bootstrap_well]My Content[/bootstrap_well]", "Well", "Well"];

      editor.ui.addRichCombo( 'bootstrap',
         {
            label : "Bootstrap",
            title :"Bootstrap",
            voiceLabel : "Bootstrap",
            className : 'cke_format',
            multiSelect : false,

            panel :
            {
               css : [ config.contentsCss,  CKEDITOR.skin.path() + 'editor.css'  ],
               voiceLabel : lang.panelVoiceLabel
            },

            init : function()
            {
               this.startGroup( "Bootstrap" );
               for (var this_tag in tags){
                  this.add(tags[this_tag][0], tags[this_tag][1], tags[this_tag][2]);
               }
            },

            onClick : function( value )
            {         
               editor.focus();
               editor.fire( 'saveSnapshot' );
               editor.insertHtml(unescape(value));
               editor.fire( 'saveSnapshot' );
            }
         });
   }
});