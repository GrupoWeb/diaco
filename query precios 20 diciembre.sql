select distinct producto.id_producto as code, producto.nombre as name, categoria.id_Categoria
      from diaco_vaciadocba vaciado
            INNER JOIN diaco_productocba producto on producto.id_producto = vaciado.idProducto 
            INNER JOIN diaco_medida medida on medida.id_medida = vaciado.idMedida
            INNER JOIN diaco_usuario usuario on usuario.id_usuario = vaciado.idVerificador
            INNER JOIN diaco_sede sede on usuario.id_sede_diaco = sede.id_diaco_sede
            INNER JOIN diaco_plantillascba plantillas on plantillas.idProducto = vaciado.idProducto
            INNER JOIN diaco_categoriacba categoria on categoria.id_Categoria = plantillas.idCategoria
      WHERE sede.id_diaco_sede = 1 AND categoria.id_Categoria = 2
            AND vaciado.created_at <= DATEADD(DAY,1,vaciado.created_at)
      GROUP by producto.id_producto, producto.nombre,categoria.id_Categoria
      ORDER BY producto.id_producto

exec getCategoriesForDepartament 1