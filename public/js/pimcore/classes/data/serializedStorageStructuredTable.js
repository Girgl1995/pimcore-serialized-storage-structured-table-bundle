pimcore.registerNS('pimcore.object.classes.data.serializedStorageStructuredTable');

pimcore.object.classes.data.serializedStorageStructuredTable =
    Class.create(pimcore.object.classes.data.structuredTable, {

        initialize: function ($super, ...args) {
            $super(...args);

            this.type = 'serializedStorageStructuredTable';
        },


        getTypeName: function () {
            return t('serializedStorageStructuredTable');
        },

    });
