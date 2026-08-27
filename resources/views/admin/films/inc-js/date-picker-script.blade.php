<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.10/dist/js/tempus-dominus.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('reservationdate');

        if (!container) {
            return;
        }

        const picker = new tempusDominus.TempusDominus(container, {
            display: {
                components: {
                    clock: false
                }
            },
            localization: {
                format: 'dd.MM.yyyy'
            }
        });

        const toggleIcon = container.querySelector(
            '[data-toggle="datetimepicker"]'
        );

        if (toggleIcon) {
            toggleIcon.addEventListener('click', function () {
                picker.toggle();
            });
        }
    });
</script>
