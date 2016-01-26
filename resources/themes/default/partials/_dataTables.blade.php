@push('scripts')
    <script>
        // DataTable
        $(function() {
            $("#table").DataTable({
                order: [['{{ $column }}', "asc"]],
                dom: '<"top">Bfrt<"bottom"lip><"clear">',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                "language": {
                    "emptyTable": " "
                }
            });
        });
</script>
@endpush
