Overview of system related internals used or defined by the extension 'extracache'
------------------------------------------------------------------------------------------------------------------------

1) Error codes as delivered to Tx_Extbase_Validation_Validator_AbstractValidator:addError()

	* Tx_Extracache_Validation_Validator_Argument
		+ 1289897741: checkName() -> 'name is not valid'
		+ 1289897742: checkType() -> 'type is not supported'
		+ 1289897743: checkValue() -> 'value is not valid (must be one of {TRUE, is_array, is_string})'
		+ 1289897744: checkValue() -> 'value is an empty array (must have items if is_array)'

	* Tx_Extracache_Validation_Validator_CleanerStrategy
		+ 1289897851: isValid() -> 'cleanerStrategy with key does not exist'
		* 1289897852: actionsAreValid() -> 'actions do not contain any valid action'
		* 1289897853: childrenModeIsValid() -> 'childrenMode is not supported'
		* 1289897854: elementModeIsValid() -> 'elementMode is not supported'

	* Tx_Extracache_Validation_Validator_Event
		+ 1289898441: isValid() -> 'event with key does already exist'
