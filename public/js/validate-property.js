var bookMe = window.bookMe || {};
bookMe.property = {};
bookMe.property.errors = [];
bookMe.property.form = $("form#property-form")
bookMe.property.errorList = $('ul.error-container');

/**
 * Validate form fields.
 *
 */
bookMe.property.validate = function()
{
    bookMe.property.errors = [];
    bookMe.property.hasError = false;

    var propertyName = $('input[name="name"]').val();

    bookMe.property.checkRequired([propertyName]);

    if(bookMe.property.errors.length > 0)
    {
        bookMe.property.showErrors();
        return false;
    }

    bookMe.property.errorList.hide();
    bookMe.property.errorList.empty();
    return true;
};

/**
 * Check that required fields are filled out.
 *
 */
bookMe.property.checkRequired = function(fields)
{
    for(var i=0; i < fields.length; i++)
    {
        if(!fields[i])
        {
            bookMe.property.hasError = true;
            bookMe.property.errors.push('Please fill out all required fields.');
            return false;
        }
    }
    return true;
};

/**
 * Display error messages.
 *
 */
bookMe.property.showErrors = function()
{
    bookMe.property.errorList.hide();
    bookMe.property.errorList.empty();
    bookMe.property.errorList.show();
    for(var i=0; i < bookMe.property.errors.length; i++)
    {
        bookMe.property.errorList.append('<li>' + bookMe.property.errors[i] + '</li>');
    }
};

/**
 * Prevent default form submit action.
 *
 */
bookMe.property.form.submit(function(e)
{
    if(!bookMe.property.validate())
    {
        bookMe.property.showErrors();
        e.preventDefault();
    }
});