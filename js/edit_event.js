    // Team size validation
        const teamSelect = document.getElementById('teamEventSelect');
        const minInput = document.getElementById('minTeamSize');
        const maxInput = document.getElementById('maxTeamSize');

        if(teamSelect && minInput && maxInput) {
            teamSelect.addEventListener('change', function() {
                if(this.value === '0') {
                    minInput.value = 1;
                    maxInput.value = 1;
                    minInput.readOnly = true;
                    maxInput.readOnly = true;
                } else {
                    minInput.readOnly = false;
                    maxInput.readOnly = false;
                }
            });
        }

        // Prevent past dates
        const dateInput = document.querySelector('input[name="proposed_date"]');
        if(dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }

        // Confirm before leaving with unsaved changes
        let formChanged = false;
        const form = document.querySelector('form');
        const formInputs = form.querySelectorAll('input, select, textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('change', function() {
                formChanged = true;
            });
        });

        window.addEventListener('beforeunload', function(e) {
            if(formChanged) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        form.addEventListener('submit', function() {
            formChanged = false;
        });