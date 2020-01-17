<?php

namespace Propel\Generator\Builder\Om;

class CustomObjectBuilder extends ObjectBuilder
{

    /**
     * Adds class phpdoc comment and opening of class.
     *
     * @param string &$script
     */
    protected function addClassOpen(&$script)
    {
		
		parent::addClassOpen($script);
		$script = str_replace('implements ActiveRecordInterface', 'implements ActiveRecordInterface, \\JsonSerializable', $script);
    }
	
    /**
     * @param string &$script
     * @see ObjectBuilder::addClassBody()
     */
    protected function addClassBody(&$script)
    {
        parent::addClassBody($script);
		
		$this->addJsonSerialize($script);
        $this->addGetArrayCamposModificados($script);
    }

    /**
     * Adiciona a implementação do método jsonSerialize
     * @param string &$script The script will be modified in this method.
     */
    protected function addJsonSerialize(&$script)
    {
        $script .= "

    public function jsonSerialize()
    {
        return \$this->toArray();
    }
";
    } // addJsonSerialize

    /**
     * Adiciona a implementação do método jsonSerialize
     * @param string &$script The script will be modified in this method.
     */
    protected function addGetArrayCamposModificados(&$script)
    {
        $script .= "
    /**
     * Retorna um array com os valores novo e anterior dos campos modificados.
     * @param ".$this->getObjectClassName(true)." Objeto antes das modificações de valores de campos
     * @return array Array de campos modificados
     */
    public function getArrayCampoMoficados(\$objAntigo)
    {
        \$arrCamposModificados = array();
        \$tableMap = \$this::TABLE_MAP;

        \$arrCamposModificados['id'] = \$this->id;
        foreach (\$this->getModifiedColumns() as \$campoModificado) {

            \$strNomeCampo = \$tableMap::getTableMap()->getColumn(\$campoModificado)->getName();
            \$item = [
                \"valorAnterior\"=>\$objAntigo->{\$strNomeCampo},
                \"valorNovo\"=>\$this->{\$strNomeCampo}
            ];
            \$arrCamposModificados[\$strNomeCampo] = \$item;
        }
        return \$arrCamposModificados;
    }
";
    } // addGetArrayCamposModificados
}
