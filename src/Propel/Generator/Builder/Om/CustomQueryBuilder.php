<?php
namespace Propel\Generator\Builder\Om;

class CustomQueryBuilder extends QueryBuilder
{
    
    /**
     * @see QueryBuilder::addClassBody()
     */
    protected function addClassBody(&$script)
    {
		parent::addClassBody($script);
		
        $this->declareClasses(
			'\utfpr\londrina\util\DataHandler'
		);
		
		$this->addFilterByEnumFieldLike($script);
		$this->addOrderByEnumFieldStringRepresentation($script);
    }

	/**
     * Adiciona o método filterByEnumFieldLike
     * @param string &$script The script will be modified in this method.
     */
    protected function addFilterByEnumFieldLike(&$script)
    {
        $script .= "
    /**
     * Adiciona criteria de filtro pela representação string do valor de um campo do tipo Enumerated.
     * @param string \$fieldName Nome do campo enumerado a ser utilizado no filtro
     * @param string \$search Valor a ser utilizado no filtro
	 * @return " . $this->getClassNameFromBuilder($this->getNewStubQueryBuilder($this->getTable())) . "
     */
	public function filterByEnumFieldLike(\$fieldName, \$search)
    {
        \$arrBusca = array();
        foreach ({$this->getTableMapClass()}::getValueSet(\$fieldName) as \$valor) {

			if (strpos(DataHandler::removeAccents(strtolower(\$valor)), DataHandler::removeAccents(strtolower(\$search))) !== false) {

                array_push(\$arrBusca, \$valor);
            }
        }

        if (sizeof(\$arrBusca) > 0) {

			return \$this->{'filterBy' . {$this->getTableMapClass()}::getTableMap()->getColumn(\$fieldName)->getPhpName()}(\$arrBusca);
        } else {

            return \$this;
        }
    }
";
    } // addFilterByEnumFieldLike
	
	/**
     * Adiciona o método orderByEnumFieldStringRepresentation
     * @param string &$script The script will be modified in this method.
     */
    protected function addOrderByEnumFieldStringRepresentation(&$script)
    {
        $script .= "
    /**
     * Adiciona criteria de ordenação pela representação string do valor de um campo do tipo Enumerated.
     * @param string \$fieldName Nome do campo enumerado a ser utilizado no filtro
     * @param string \$orderByDirection Direção do ordenação (ASC ou DESC)
	 * @return " . $this->getClassNameFromBuilder($this->getNewStubQueryBuilder($this->getTable())) . "
     */
	public function orderByEnumFieldStringRepresentation(\$fieldName, \$orderByDirection)
    {
		\$orderByColumnName = str_replace({$this->getTableMapClass()}::TABLE_NAME . '.', '', \$fieldName) . 'Str';
        \$intCont = 0;
        \$strOrderBy = 'case ' . \$fieldName;
        foreach ({$this->getTableMapClass()}::getValueSet(\$fieldName) as \$valor) {

            \$strOrderBy .= ' when ' . \$intCont . ' then \'' . \$valor . '\'';
            \$intCont++;
        }
        \$strOrderBy .= ' else \'\' end';
		\$this->withColumn(\$strOrderBy, \$orderByColumnName);

        if (strpos(strtoupper(\$orderByDirection), 'ASC') === false) {

            return \$this->addDescendingOrderByColumn(\$orderByColumnName);
        } else {

            return \$this->addAscendingOrderByColumn(\$orderByColumnName);
        }
    }
";
    } // addOrderByEnumFieldStringRepresentation
}
