/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

( e => {
const { [ 'en-gb' ]: { dictionary, getPluralForm } } = {"en-gb":{"dictionary":{"Words: %0":"Words: %0","Characters: %0":"Characters: %0","Upload in progress":"Upload in progress","Undo":"Undo","Redo":"Redo","Rich Text Editor":"Rich Text Editor","Editor editing area: %0":"","Edit block":"Edit block","Click to edit block":"","Drag to move":"","Next":"Next","Previous":"Previous","Editor toolbar":"","Dropdown toolbar":"","Black":"Black","Dim grey":"Dim grey","Grey":"Grey","Light grey":"Light grey","White":"White","Red":"Red","Orange":"Orange","Yellow":"Yellow","Light green":"Light green","Green":"Green","Aquamarine":"Aquamarine","Turquoise":"Turquoise","Light blue":"Light blue","Blue":"Blue","Purple":"Purple","Editor block content toolbar":"","Editor contextual toolbar":"","HEX":"","No results found":"","No searchable items":"","Editor dialog":"","Close":"","Help Contents. To close this dialog press ESC.":"","Below, you can find a list of keyboard shortcuts that can be used in the editor.":"","(may require <kbd>Fn</kbd>)":"","Accessibility":"","Accessibility help":"","Press %0 for help.":"","Move focus in and out of an active dialog window":"","MENU_BAR_MENU_FILE":"","MENU_BAR_MENU_EDIT":"","MENU_BAR_MENU_VIEW":"","MENU_BAR_MENU_INSERT":"","MENU_BAR_MENU_FORMAT":"","MENU_BAR_MENU_TOOLS":"","MENU_BAR_MENU_HELP":"","MENU_BAR_MENU_TEXT":"","MENU_BAR_MENU_FONT":"","Editor menu bar":"","Please enter a valid color (e.g. \"ff0000\").":"","Insert table":"Insert table","Header column":"Header column","Insert column left":"Insert column left","Insert column right":"Insert column right","Delete column":"Delete column","Select column":"","Column":"Column","Header row":"Header row","Insert row below":"Insert row below","Insert row above":"Insert row above","Delete row":"Delete row","Select row":"","Row":"Row","Merge cell up":"Merge cell up","Merge cell right":"Merge cell right","Merge cell down":"Merge cell down","Merge cell left":"Merge cell left","Split cell vertically":"Split cell vertically","Split cell horizontally":"Split cell horizontally","Merge cells":"Merge cells","Table toolbar":"","Table properties":"","Cell properties":"","Border":"","Style":"","Width":"","Height":"","Color":"","Background":"","Padding":"","Dimensions":"","Table cell text alignment":"","Alignment":"","Horizontal text alignment toolbar":"","Vertical text alignment toolbar":"","Table alignment toolbar":"","None":"","Solid":"","Dotted":"","Dashed":"","Double":"","Groove":"","Ridge":"","Inset":"","Outset":"","Align cell text to the left":"","Align cell text to the center":"","Align cell text to the right":"","Justify cell text":"","Align cell text to the top":"","Align cell text to the middle":"","Align cell text to the bottom":"","Align table to the left":"","Center table":"","Align table to the right":"","The color is invalid. Try \"#FF0000\" or \"rgb(255,0,0)\" or \"red\".":"","The value is invalid. Try \"10px\" or \"2em\" or simply \"2\".":"","Color picker":"","Enter table caption":"","Keystrokes that can be used in a table cell":"","Move the selection to the next cell":"","Move the selection to the previous cell":"","Insert a new table row (when in the last cell of a table)":"","Navigate through the table":"","Table":"","Disable editing":"","Enable editing":"Enable editing","Previous editable region":"Previous editable region","Next editable region":"Next editable region","Navigate editable regions":"Navigate editable regions","Remove Format":"Remove Format","media widget":"Media widget","Media URL":"Media URL","Paste the media URL in the input.":"Paste the media URL in the input.","Tip: Paste the URL into the content to embed faster.":"Tip: Paste the URL into the content to embed faster.","The URL must not be empty.":"The URL must not be empty.","This media URL is not supported.":"This media URL is not supported.","Insert media":"Insert media","Media":"","Media toolbar":"","Open media in new tab":"","Numbered List":"Numbered List","Bulleted List":"Bulleted List","To-do List":"","Bulleted list styles toolbar":"","Numbered list styles toolbar":"","Toggle the disc list style":"","Toggle the circle list style":"","Toggle the square list style":"","Toggle the decimal list style":"","Toggle the decimal with leading zero list style":"","Toggle the lower–roman list style":"","Toggle the upper–roman list style":"","Toggle the lower–latin list style":"","Toggle the upper–latin list style":"","Disc":"","Circle":"","Square":"","Decimal":"","Decimal with leading zero":"","Lower–roman":"","Upper-roman":"","Lower-latin":"","Upper-latin":"","List properties":"","Start at":"","Invalid start index value.":"","Start index must be greater than 0.":"","Reversed order":"","Keystrokes that can be used in a list":"","Increase list item indent":"","Decrease list item indent":"","Entering a to-do list":"","Leaving a to-do list":"","Unlink":"Unlink","Link":"Link","Link URL":"Link URL","Link URL must not be empty.":"","Link image":"","Edit link":"Edit link","Open link in new tab":"Open link in new tab","This link has no URL":"This link has no URL","Open in a new tab":"Open in a new tab","Downloadable":"Downloadable","Create link":"","Move out of a link":"","Increase indent":"Increase indent","Decrease indent":"Decrease indent","image widget":"Image widget","Wrap text":"","Break text":"","In line":"","Side image":"Side image","Full size image":"Full size image","Left aligned image":"Left aligned image","Centered image":"Centred image","Right aligned image":"Right aligned image","Change image text alternative":"Change image text alternative","Text alternative":"Text alternative","Enter image caption":"Enter image caption","Insert image":"Insert image","Replace image":"","Upload from computer":"","Replace from computer":"","Upload image from computer":"","Image from computer":"","From computer":"","Replace image from computer":"","Upload failed":"Upload failed","Image toolbar":"","Resize image":"","Resize image to %0":"","Resize image to the original size":"","Resize image (in %0)":"","Original":"","Custom image size":"","Custom":"","Image resize list":"","Insert image via URL":"","Insert via URL":"","Image via URL":"","Via URL":"","Update image URL":"","Caption for the image":"","Caption for image: %0":"","The value must not be empty.":"","The value should be a plain number.":"","Uploading image":"","Image upload complete":"","Error during image upload":"","Image":"","Yellow marker":"Yellow marker","Green marker":"Green marker","Pink marker":"Pink marker","Blue marker":"Blue marker","Red pen":"Red pen","Green pen":"Green pen","Remove highlight":"Remove highlight","Highlight":"Highlight","Text highlight toolbar":"","Paragraph":"Paragraph","Heading":"Heading","Choose heading":"Choose heading","Heading 1":"Heading 1","Heading 2":"Heading 2","Heading 3":"Heading 3","Heading 4":"Heading 4","Heading 5":"Heading 5","Heading 6":"Heading 6","Type your title":"","Type or paste your content here.":"","Font Size":"Font Size","Tiny":"Tiny","Small":"Small","Big":"Big","Huge":"Huge","Font Family":"Font Family","Default":"Default","Font Color":"Font Colour","Font Background Color":"Font Background Colour","Document colors":"Document colours","Cancel":"Cancel","Clear":"","Remove color":"Remove colour","Restore default":"","Save":"Save","Show more items":"","%0 of %1":"%0 of %1","Cannot upload file:":"Cannot upload file:","Rich Text Editor. Editing area: %0":"","Insert with file manager":"","Replace with file manager":"","Insert image with file manager":"","Replace image with file manager":"","File":"","With file manager":"","Toggle caption off":"","Toggle caption on":"","Content editing keystrokes":"","These keyboard shortcuts allow for quick access to content editing features.":"","User interface and content navigation keystrokes":"","Use the following keystrokes for more efficient navigation in the CKEditor 5 user interface.":"","Close contextual balloons, dropdowns, and dialogs":"","Open the accessibility help dialog":"","Move focus between form fields (inputs, buttons, etc.)":"","Move focus to the menu bar, navigate between menu bars":"","Move focus to the toolbar, navigate between toolbars":"","Navigate through the toolbar or menu bar":"","Execute the currently focused button. Executing buttons that interact with the editor content moves the focus back to the content.":"","Accept":"","Insert image or file":"Insert image or file","Could not obtain resized image URL.":"Could not obtain resized image URL.","Selecting resized image failed":"Selecting resized image failed","Could not insert image at the current position.":"Could not insert image at the current position.","Inserting image failed":"Inserting image failed","Block quote":"Block quote","Bold":"Bold","Italic":"Italic","Underline":"Underline","Code":"Code","Strikethrough":"Strikethrough","Subscript":"Subscript","Superscript":"Superscript","Italic text":"","Move out of an inline code style":"","Bold text":"","Underline text":"","Strikethrough text":"","Saving changes":"Saving changes","Align left":"Align left","Align right":"Align right","Align center":"Align center","Justify":"Justify","Text alignment":"Text alignment","Text alignment toolbar":""},getPluralForm(n){return (n != 1);}}};
e[ 'en-gb' ] ||= { dictionary: {}, getPluralForm: null };
e[ 'en-gb' ].dictionary = Object.assign( e[ 'en-gb' ].dictionary, dictionary );
e[ 'en-gb' ].getPluralForm = getPluralForm;
} )( window.CKEDITOR_TRANSLATIONS ||= {} );
