/*
 * Point d'entrée principal de l'application
 */

// Importations CSS
import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'select2/dist/css/select2.min.css';
import 'toastr/build/toastr.min.css';

// Importations JavaScript
import 'bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

import 'datatables.net';
import 'datatables.net-bs5';
import 'select2';
import toastr from 'toastr';
window.toastr = toastr;

import Chart from 'chart.js/auto';
window.Chart = Chart;

import Swal from 'sweetalert2';
window.Swal = Swal;

import moment from 'moment';
window.moment = moment;
moment.locale('fr');

// Configuration globale de toastr
toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 5000,
    extendedTimeOut: 2000,
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
};

// Configuration globale de DataTables
$.extend(true, $.fn.dataTable.defaults, {
    language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
    },
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Tous']],
    responsive: true,
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
});

// Configuration globale de Select2
$.fn.select2.defaults.set('theme', 'bootstrap-5');
$.fn.select2.defaults.set('language', 'fr');

// Fonctions utilitaires globales
window.PharmaUtils = {
    /**
     * Formater un nombre en devise TND
     */
    formatCurrency(amount) {
        return new Intl.NumberFormat('fr-TN', {
            style: 'currency',
            currency: 'TND',
            minimumFractionDigits: 2
        }).format(amount);
    },

    /**
     * Formater une date
     */
    formatDate(date, format = 'DD/MM/YYYY') {
        return moment(date).format(format);
    },

    /**
     * Formater une date avec l'heure
     */
    formatDateTime(date, format = 'DD/MM/YYYY HH:mm') {
        return moment(date).format(format);
    },

    /**
     * Confirmer une action
     */
    confirm(title, text, confirmText = 'Oui', cancelText = 'Non') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        });
    },

    /**
     * Afficher un message de succès
     */
    success(message, title = 'Succès') {
        toastr.success(message, title);
    },

    /**
     * Afficher un message d'erreur
     */
    error(message, title = 'Erreur') {
        toastr.error(message, title);
    },

    /**
     * Afficher un message d'avertissement
     */
    warning(message, title = 'Attention') {
        toastr.warning(message, title);
    },

    /**
     * Afficher un message d'information
     */
    info(message, title = 'Information') {
        toastr.info(message, title);
    },

    /**
     * Charger un spinner
     */
    showLoading(text = 'Chargement en cours...') {
        Swal.fire({
            title: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Cacher le spinner
     */
    hideLoading() {
        Swal.close();
    },

    /**
     * Effectuer une requête AJAX
     */
    async ajax(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('AJAX Error:', error);
            this.error('Une erreur est survenue lors de la requête');
            throw error;
        }
    },

    /**
     * Débounce une fonction
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Copier du texte dans le presse-papiers
     */
    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.success('Copié dans le presse-papiers');
        } catch (err) {
            this.error('Impossible de copier');
        }
    },

    /**
     * Télécharger un fichier
     */
    downloadFile(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};

// Initialisation au chargement du DOM
$(document).ready(function() {
    // Initialiser les DataTables
    if ($.fn.DataTable) {
        $('.datatable').each(function() {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable();
            }
        });
    }

    // Initialiser Select2
    if ($.fn.select2) {
        $('.select2').select2({
            width: '100%'
        });
    }

    // Tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Popovers Bootstrap
    const popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Confirmation de suppression
    $('form[data-confirm]').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const message = $(this).data('confirm') || 'Êtes-vous sûr de vouloir supprimer cet élément?';
        
        PharmaUtils.confirm('Confirmation', message, 'Oui, supprimer', 'Annuler')
            .then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
    });

    // Liens de confirmation
    $('a[data-confirm]').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        const message = $(this).data('confirm') || 'Êtes-vous sûr?';
        
        PharmaUtils.confirm('Confirmation', message)
            .then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
    });

    // Bouton retour
    $('.btn-back').on('click', function(e) {
        e.preventDefault();
        window.history.back();
    });

    // Auto-focus premier input
    $('.auto-focus input:first, .auto-focus select:first, .auto-focus textarea:first').focus();

    // Formater les montants
    $('.format-currency').each(function() {
        const value = parseFloat($(this).text());
        if (!isNaN(value)) {
            $(this).text(PharmaUtils.formatCurrency(value));
        }
    });

    // Formater les dates
    $('.format-date').each(function() {
        const date = $(this).text();
        $(this).text(PharmaUtils.formatDate(date));
    });

    // Toggle sidebar sur mobile
    $('#sidebarToggle').on('click', function() {
        $('.sidebar').toggleClass('show');
    });

    // Fermer sidebar en cliquant en dehors (mobile)
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.sidebar, #sidebarToggle').length) {
            $('.sidebar').removeClass('show');
        }
    });

    // Recherche en temps réel
    $('[data-live-search]').on('input', PharmaUtils.debounce(function() {
        const query = $(this).val();
        const target = $(this).data('live-search');
        
        if (query.length < 2) return;
        
        PharmaUtils.ajax(target, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(data => {
            // Traiter les résultats
            console.log('Search results:', data);
        });
    }, 500));

    // Print
    $('.btn-print').on('click', function(e) {
        e.preventDefault();
        window.print();
    });

    // Auto-hide alerts
    $('.alert:not(.alert-permanent)').delay(5000).fadeOut(300);

    console.log('✓ PharmaPro Application initialized successfully');
});

// Gestion des erreurs globales
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
});

// Export pour utilisation dans d'autres modules
export default PharmaUtils;
