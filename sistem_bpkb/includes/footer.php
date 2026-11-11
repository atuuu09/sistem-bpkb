<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>

    <style>
        /* Mengubah warna biru bawaan Bootstrap menjadi Oranye MACF pada tombol nomor halaman */
        .pagination .page-item.active .page-link {
            background-color: #F57C00 !important; /* Oranye MACF */
            border-color: #F57C00 !important;
            color: #FFFFFF !important;
        }
        
        .pagination .page-link {
            color: #F57C00 !important;
        }
        
        .pagination .page-link:hover, 
        .pagination .page-link:focus {
            color: #E65100 !important; /* Oranye pekat */
            background-color: #FFF3E0 !important; /* Latar belakang oranye pudar saat disorot */
            border-color: #FFCC80 !important;
            box-shadow: none !important;
        }
    </style>
</body>
</html>