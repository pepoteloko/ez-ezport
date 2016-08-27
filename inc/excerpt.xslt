<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="text" encoding="UTF-8" indent="no" omit-xml-declaration="yes" />
	<xsl:strip-space elements="*" />
	<xsl:template match="section">
		<xsl:apply-templates />
	</xsl:template>
	<xsl:template match="paragraph">
		<xsl:value-of select="."/>
	</xsl:template>
</xsl:stylesheet>