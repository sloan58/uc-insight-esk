@push('scripts')
    <script>
        // DataTable
        $(function() {
            $("#table").DataTable({
                order: [['{{ $column }}', "dec"]],
                dom: '<"top">Bfrt<"bottom"lip><"clear">',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });
</script>
@endpush
