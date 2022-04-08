var ComponentsBootstrapMaxlength = function () {

    var handleBootstrapMaxlength = function() {
        
        $('#message').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#message1').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#message2').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#vehicledesc').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#vehicleurl').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#propertydesc').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#propertyurl').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#ifmember_message').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#autoresponder1').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#autoresponder2').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#autoresponder3').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#autoresponder4').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
         $('#autoreply').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
         $('#addpoints').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
         $('#reachedatgoal').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
         $('#checkstatus').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
        
        $('#nonkeyword_autoresponse').maxlength({
            alwaysShow: true,
            twoCharLinebreak: false,
            warningClass: "label label-success",
            limitReachedClass: "label label-danger",
            separator: ' out of ',
            preText: 'You typed ',
            postText: ' characters',
            validate: true
            
        });
    };

    return {
        //main function to initiate the module
        init: function () {
            handleBootstrapMaxlength();
        }
    };

}();

jQuery(document).ready(function() {    
   ComponentsBootstrapMaxlength.init(); 
});