$(document).ready(function () {

    // CK Editor
    ClassicEditor
        .create(document.querySelector('#body'))
        .catch(error => {
            console.error(error);
        });

    // CheckAllBoxes for Admin View Posts
    $('#selectAllBoxes').click(function (event) {

        if (this.checked) {
            $('.checkBoxes').each(function () {
                this.checked = true;
            });
        } else {
            $('.checkBoxes').each(function () {
                this.checked = false;
            });
        }

    });

});

