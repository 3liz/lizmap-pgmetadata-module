

var PgMetadata = {

    check : function()
    {
        let url = document.getElementById('pgmetadata').getAttribute('data-check-url');
        fetch(url, {
            method: 'GET',
        })
            .then(function(response) {

                if (!response.ok) {
                    alert('Unexpected error');
                    PgMetadata.showStatus('no_profile_no_view');
                    return Promise.reject();
                }
                return response.json();
            })
            .then(function(results) {
                PgMetadata.showStatus(results.reason);
            })
            .catch(function(error) {
                alert('Network error');
                PgMetadata.showStatus('no_profile_no_view');
            });
    },

    saveNewCredentials: function () {

        let formElem = document.getElementById('pgmd-profile');
        let url = formElem.getAttribute('action');
        let formData =  new FormData(formElem);

        PgMetadata.showStatus('saving');

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(function(response) {

                if (!response.ok) {
                    alert('Unexpected error');
                    PgMetadata.showStatus('no_profile_no_view');
                    return Promise.reject();
                }
                return response.json();
            })
            .then(function(results) {
                PgMetadata.showStatus(results.reason);

            })
            .catch(function(error) {
                alert('Network error');
                PgMetadata.showStatus('no_profile_no_view');
            });
    },

    showStatus: function(reason)
    {
        this.hideAll();

        let list = document.querySelectorAll('.pgmd-'+reason);
        list.forEach((item) => {
            item.classList.add('pgmd-show');
        });
    },

    hideAll: function()
    {
        let list = document.querySelectorAll('.pgmd-show');
        list.forEach((item) => {
            item.classList.remove('pgmd-show');
        });
    }
}


window.addEventListener('load', function() {

    PgMetadata.check();

    document.getElementById('pgmd-profile').addEventListener('submit',
        function(ev) {
            ev.preventDefault();
            PgMetadata.saveNewCredentials()
        });
    document.getElementById('pgmd-modify-credentials').addEventListener('click',
        function (ev) {
            PgMetadata.showStatus('modify_credentials');
        });
    document.getElementById('pgmd-create-credentials').addEventListener('click',
        function (ev) {
            PgMetadata.showStatus('create_credentials');
        });
});






